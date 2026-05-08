/**
 * ═══════════════════════════════════════════════════════════════
 *  BudgetFamille – Service Worker
 *  Strategy:
 *    • Cache First  → static assets (CSS, JS, fonts, images)
 *    • Network First → pages & API GET (cache as fallback)
 *    • Background Sync → offline expense submissions
 *    • Push Notifications → budget alerts
 * ═══════════════════════════════════════════════════════════════
 */

const CACHE_NAME    = 'budgetfamille-v2';
const OFFLINE_URL   = '/offline';
const DB_NAME       = 'budgetfamille_db';
const DB_VERSION    = 2;
const QUEUE_STORE   = 'offline_queue';

// ── Assets cached on install (Cache First) ───────────────────────
const STATIC_ASSETS = [
    '/offline',
    '/manifest.json',
    '/icons/icon-192.png',
    '/icons/icon-512.png',
    // CDN assets – cached on first use via cacheFirst() below
];

// ── Pages pre-cached so offline navigation works ─────────────────
const PAGES_TO_PRECACHE = [
    '/dashboard',
    '/expenses',
    '/expenses/create',
    '/budgets',
];

// ─────────────────────────────────────────────────────────────────
//  INSTALL – cache shell assets
// ─────────────────────────────────────────────────────────────────
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => cache.addAll(STATIC_ASSETS))
            .then(() => self.skipWaiting())
            .catch((err) => console.warn('[SW] Install cache error:', err))
    );
});

// ─────────────────────────────────────────────────────────────────
//  ACTIVATE – purge old caches
// ─────────────────────────────────────────────────────────────────
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys()
            .then((keys) => Promise.all(
                keys.filter((k) => k !== CACHE_NAME).map((k) => caches.delete(k))
            ))
            .then(() => self.clients.claim())
    );
});

// ─────────────────────────────────────────────────────────────────
//  FETCH – routing strategies
// ─────────────────────────────────────────────────────────────────
self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);

    // Only handle http/https
    if (!url.protocol.startsWith('http')) return;

    // Skip non-GET (POST queued via Background Sync)
    if (request.method !== 'GET') return;

    // Skip debug/admin routes
    if (/\/_debugbar|\/horizon|\/telescope/.test(url.pathname)) return;

    // ── Static assets (CSS, JS, fonts, images, CDN) → Cache First ──
    if (isStaticAsset(request)) {
        event.respondWith(cacheFirst(request));
        return;
    }

    // ── Everything else → Network First ───────────────────────────
    event.respondWith(networkFirst(request));
});

/** Determine if request is a static/immutable asset */
function isStaticAsset(request) {
    const url = new URL(request.url);
    return (
        url.pathname.match(/\.(css|js|woff2?|ttf|eot|svg|png|jpg|jpeg|gif|ico|webp|map)(\?.*)?$/) ||
        url.hostname !== self.location.hostname // External CDN
    );
}

/**
 * Cache First – serve from cache, fetch & cache on miss
 */
async function cacheFirst(request) {
    const cached = await caches.match(request);
    if (cached) return cached;

    try {
        const response = await fetch(request);
        if (response.ok) {
            const cache = await caches.open(CACHE_NAME);
            cache.put(request, response.clone());
        }
        return response;
    } catch {
        // Nothing useful to return for an asset miss
        return new Response('', { status: 503, statusText: 'Service Unavailable' });
    }
}

/**
 * Network First – fetch, update cache; fallback to cache or offline page
 */
async function networkFirst(request) {
    try {
        const response = await fetch(request);
        if (response.ok) {
            const cache = await caches.open(CACHE_NAME);
            cache.put(request, response.clone());
        }
        return response;
    } catch {
        const cached = await caches.match(request);
        if (cached) return cached;

        // Navigation request → offline page
        if (request.mode === 'navigate') {
            const offlinePage = await caches.match(OFFLINE_URL);
            return offlinePage || new Response(inlineOfflineHTML(), {
                headers: { 'Content-Type': 'text/html; charset=utf-8' },
            });
        }

        // API/JSON request → structured error
        return new Response(
            JSON.stringify({ error: true, message: 'Vous êtes hors ligne. Données non disponibles.' }),
            { status: 503, headers: { 'Content-Type': 'application/json' } }
        );
    }
}

// ─────────────────────────────────────────────────────────────────
//  BACKGROUND SYNC – flush offline expense queue
// ─────────────────────────────────────────────────────────────────
self.addEventListener('sync', (event) => {
    if (event.tag === 'sync-expenses') {
        event.waitUntil(flushExpenseQueue());
    }
});

async function flushExpenseQueue() {
    const db    = await openDB();
    const queue = await getAllFromStore(db, QUEUE_STORE);

    if (!queue.length) return;

    let synced  = 0;
    let failed  = 0;

    for (const item of queue) {
        try {
            const response = await fetch('/api/expenses/sync', {
                method: 'POST',
                headers: {
                    'Content-Type':  'application/json',
                    'X-CSRF-TOKEN':  item.csrfToken,
                    'Accept':        'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify(item.data),
                credentials: 'include',
            });

            if (response.ok) {
                await deleteFromStore(db, QUEUE_STORE, item.id);
                synced++;
            } else {
                // Permanent error (validation) – remove from queue too
                const payload = await response.json().catch(() => ({}));
                if (response.status < 500) {
                    await deleteFromStore(db, QUEUE_STORE, item.id);
                }
                failed++;
            }
        } catch {
            failed++;
            // Will retry on next sync event
        }
    }

    // Notify all open tabs
    const clients = await self.clients.matchAll({ includeUncontrolled: true });
    clients.forEach((client) =>
        client.postMessage({ type: 'SYNC_DONE', synced, failed })
    );
}

// ─────────────────────────────────────────────────────────────────
//  PUSH NOTIFICATIONS
// ─────────────────────────────────────────────────────────────────
self.addEventListener('push', (event) => {
    const data    = event.data ? event.data.json() : {};
    const title   = data.title   || 'BudgetFamille';
    const options = {
        body:    data.body    || 'Nouvelle notification',
        icon:    '/icons/icon-192.png',
        badge:   '/icons/icon-192.png',
        vibrate: [100, 50, 100],
        data:    { url: data.url || '/dashboard' },
        actions: [
            { action: 'open',  title: 'Voir' },
            { action: 'close', title: 'Ignorer' },
        ],
    };
    event.waitUntil(self.registration.showNotification(title, options));
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    if (event.action !== 'close') {
        const targetUrl = event.notification.data?.url || '/dashboard';
        event.waitUntil(
            clients.matchAll({ type: 'window' }).then((windowClients) => {
                const existing = windowClients.find((c) => c.url === targetUrl && 'focus' in c);
                if (existing) return existing.focus();
                return clients.openWindow(targetUrl);
            })
        );
    }
});

// ─────────────────────────────────────────────────────────────────
//  INDEXEDDB HELPERS
// ─────────────────────────────────────────────────────────────────
function openDB() {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open(DB_NAME, DB_VERSION);

        request.onupgradeneeded = (event) => {
            const db = event.target.result;

            // Offline expense queue
            if (!db.objectStoreNames.contains(QUEUE_STORE)) {
                const store = db.createObjectStore(QUEUE_STORE, { keyPath: 'id', autoIncrement: true });
                store.createIndex('createdAt', 'createdAt', { unique: false });
            }

            // Cache store for categories/accounts (used by offline form)
            if (!db.objectStoreNames.contains('app_cache')) {
                db.createObjectStore('app_cache', { keyPath: 'key' });
            }
        };

        request.onsuccess = () => resolve(request.result);
        request.onerror   = () => reject(request.error);
    });
}

function getAllFromStore(db, storeName) {
    return new Promise((resolve, reject) => {
        const tx  = db.transaction(storeName, 'readonly');
        const req = tx.objectStore(storeName).getAll();
        req.onsuccess = () => resolve(req.result);
        req.onerror   = () => reject(req.error);
    });
}

function deleteFromStore(db, storeName, id) {
    return new Promise((resolve, reject) => {
        const tx  = db.transaction(storeName, 'readwrite');
        const req = tx.objectStore(storeName).delete(id);
        req.onsuccess = () => resolve();
        req.onerror   = () => reject(req.error);
    });
}

// ─────────────────────────────────────────────────────────────────
//  INLINE OFFLINE PAGE (last-resort fallback)
// ─────────────────────────────────────────────────────────────────
function inlineOfflineHTML() {
    return `<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Hors ligne – BudgetFamille</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; font-family: Inter, system-ui, sans-serif; }
  body { background: #f8fafc; display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 1rem; }
  .card { background: #fff; border-radius: 24px; padding: 2.5rem 2rem; text-align: center; max-width: 360px; width: 100%; box-shadow: 0 8px 32px rgba(99,102,241,.15); }
  .icon { font-size: 4rem; margin-bottom: 1rem; }
  h1 { color: #1e1b4b; font-size: 1.35rem; font-weight: 700; margin-bottom: .75rem; }
  p { color: #64748b; font-size: .9rem; line-height: 1.65; }
  .btn { display: inline-block; margin-top: 1.75rem; background: linear-gradient(135deg,#6366f1,#4f46e5); color: #fff; padding: .8rem 2rem; border-radius: 50px; text-decoration: none; font-weight: 600; font-size: .95rem; border: none; cursor: pointer; }
  .tip { margin-top: 1.25rem; font-size: .78rem; color: #94a3b8; }
</style>
</head>
<body>
  <div class="card">
    <div class="icon">📡</div>
    <h1>Vous êtes hors ligne</h1>
    <p>La connexion internet est introuvable. Vos données en attente seront synchronisées automatiquement dès votre retour en ligne.</p>
    <button class="btn" onclick="window.location.reload()">↺ Réessayer</button>
    <p class="tip">💡 Vous pouvez toujours <a href="/expenses/create" style="color:#6366f1">ajouter des dépenses</a> — elles seront enregistrées localement.</p>
  </div>
</body>
</html>`;
}
