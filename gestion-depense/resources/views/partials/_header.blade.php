{{-- =====================================================
     resources/views/partials/_header.blade.php
     Top navigation bar – included in every main view
====================================================== --}}

<header class="topbar" id="topbar">
    <div class="topbar-inner">

        {{-- Left: hamburger (mobile) + page title slot --}}
        <div class="topbar-left">
            <button class="hamburger d-lg-none" id="sidebarToggle" aria-label="Toggle sidebar">
                <i class="bi bi-list"></i>
            </button>
            <span class="topbar-title d-none d-sm-inline">
                @yield('page-title', 'Tableau de bord')
            </span>
        </div>

        {{-- Right: user block --}}
        <div class="topbar-right">

            {{-- Greeting + date --}}
            <span class="topbar-greeting d-none d-md-inline">
                <i class="bi bi-sun me-1" style="color:#f59e0b"></i>
                Bonjour, <strong>{{ auth()->user()->name }}</strong>
            </span>

            {{-- Divider --}}
            <span class="topbar-divider d-none d-md-inline"></span>

            {{-- Avatar + dropdown --}}
            <div class="user-menu dropdown">
                <button
                    class="user-avatar-btn dropdown-toggle"
                    id="userDropdown"
                    data-bs-toggle="dropdown"
                    aria-expanded="false"
                    title="{{ auth()->user()->name }}"
                >
                    <span class="avatar-circle">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </span>
                    <span class="avatar-name d-none d-md-inline">{{ auth()->user()->name }}</span>
                    <i class="bi bi-chevron-down avatar-caret d-none d-md-inline"></i>
                </button>

                <ul class="dropdown-menu dropdown-menu-end user-dropdown" aria-labelledby="userDropdown">

                    {{-- User info header --}}
                    <li class="dropdown-header-user">
                        <div class="dropdown-avatar-lg">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="dropdown-user-name">{{ auth()->user()->name }}</div>
                            <div class="dropdown-user-email">{{ auth()->user()->email }}</div>
                        </div>
                    </li>

                    <li><hr class="dropdown-divider my-1"></li>

                    {{-- Profile link --}}
                    <li>
                        <a class="dropdown-item dropdown-item-custom" href="{{ route('profile.edit') }}">
                            <i class="bi bi-person-circle"></i>
                            Mon profil
                        </a>
                    </li>

                    {{-- Expenses --}}
                    <li>
                        <a class="dropdown-item dropdown-item-custom" href="/expenses">
                            <i class="bi bi-receipt-cutoff"></i>
                            Mes dépenses
                        </a>
                    </li>

                    {{-- Categories --}}
                    <li>
                        <a class="dropdown-item dropdown-item-custom" href="/categories">
                            <i class="bi bi-tag"></i>
                            Catégories
                        </a>
                    </li>

                    <li><hr class="dropdown-divider my-1"></li>

                    {{-- Logout --}}
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item dropdown-item-custom dropdown-item-danger">
                                <i class="bi bi-box-arrow-right"></i>
                                Déconnexion
                            </button>
                        </form>
                    </li>
                </ul>
            </div>

        </div>{{-- /topbar-right --}}
    </div>{{-- /topbar-inner --}}
</header>

{{-- ── Topbar styles (scoped, injected once) ── --}}
<style>
    :root {
        --topbar-height: 62px;
        --sidebar-width: 240px;
    }

    /* ── Topbar shell ── */
    .topbar {
        position: fixed;
        top: 0;
        left: var(--sidebar-width);
        right: 0;
        height: var(--topbar-height);
        background: #fff;
        border-bottom: 1px solid #f1f5f9;
        box-shadow: 0 1px 4px rgba(0,0,0,.05);
        z-index: 999;
        transition: left .3s;
    }

    .topbar-inner {
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 1.5rem;
        gap: 1rem;
    }

    /* ── Left side ── */
    .topbar-left {
        display: flex;
        align-items: center;
        gap: .75rem;
    }

    .topbar-title {
        font-size: .9rem;
        font-weight: 600;
        color: #334155;
        letter-spacing: .01em;
    }

    /* Hamburger (mobile) */
    .hamburger {
        background: none;
        border: none;
        padding: .4rem .5rem;
        border-radius: 8px;
        font-size: 1.3rem;
        color: #475569;
        cursor: pointer;
        line-height: 1;
        transition: background .2s, color .2s;
    }

    .hamburger:hover { background: #f1f5f9; color: #1e293b; }

    /* ── Right side ── */
    .topbar-right {
        display: flex;
        align-items: center;
        gap: .85rem;
    }

    .topbar-greeting {
        font-size: .85rem;
        color: #64748b;
    }

    .topbar-divider {
        width: 1px;
        height: 24px;
        background: #e2e8f0;
    }

    /* ── Avatar button ── */
    .user-avatar-btn {
        background: none;
        border: none;
        display: flex;
        align-items: center;
        gap: .5rem;
        cursor: pointer;
        padding: .35rem .6rem;
        border-radius: 50px;
        border: 1.5px solid #e2e8f0;
        transition: all .2s;
    }

    .user-avatar-btn:hover,
    .user-avatar-btn[aria-expanded="true"] {
        background: #f8fafc;
        border-color: #c7d2fe;
    }

    /* Hide Bootstrap's default caret */
    .user-avatar-btn::after { display: none; }

    .avatar-circle {
        width: 32px; height: 32px;
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        color: #fff;
        font-size: .82rem;
        font-weight: 700;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .avatar-name {
        font-size: .875rem;
        font-weight: 600;
        color: #334155;
        max-width: 130px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .avatar-caret {
        font-size: .65rem;
        color: #94a3b8;
        transition: transform .2s;
    }

    .user-avatar-btn[aria-expanded="true"] .avatar-caret { transform: rotate(180deg); }

    /* ── Dropdown ── */
    .user-dropdown {
        min-width: 230px;
        border: none;
        border-radius: 14px;
        box-shadow: 0 10px 40px rgba(0,0,0,.12);
        padding: .5rem;
        margin-top: .35rem;
    }

    .dropdown-header-user {
        display: flex;
        align-items: center;
        gap: .75rem;
        padding: .65rem .75rem;
    }

    .dropdown-avatar-lg {
        width: 40px; height: 40px;
        background: linear-gradient(135deg, #6366f1, #4f46e5);
        color: #fff;
        font-size: .95rem;
        font-weight: 700;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .dropdown-user-name {
        font-weight: 700;
        font-size: .875rem;
        color: #1e293b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 150px;
    }

    .dropdown-user-email {
        font-size: .75rem;
        color: #94a3b8;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 150px;
    }

    .dropdown-item-custom {
        display: flex;
        align-items: center;
        gap: .6rem;
        font-size: .875rem;
        font-weight: 500;
        color: #475569;
        padding: .55rem .75rem;
        border-radius: 9px;
        transition: background .15s, color .15s;
    }

    .dropdown-item-custom:hover {
        background: #f1f5f9;
        color: #1e293b;
    }

    .dropdown-item-custom i { font-size: .95rem; color: #6366f1; }

    .dropdown-item-danger { color: #ef4444 !important; }
    .dropdown-item-danger i { color: #ef4444 !important; }
    .dropdown-item-danger:hover { background: #fef2f2 !important; color: #dc2626 !important; }

    /* ── Mobile: sidebar overlay ── */
    .sidebar-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,.4);
        z-index: 998;
    }

    .sidebar-overlay.active { display: block; }

    @media (max-width: 991.98px) {
        .topbar { left: 0; }

        .sidebar {
            transform: translateX(-100%);
            transition: transform .3s;
        }

        .sidebar.open { transform: translateX(0); }

        .main-content { margin-left: 0 !important; }
    }
</style>

{{-- Sidebar overlay (mobile) --}}
<div class="sidebar-overlay" id="sidebarOverlay"></div>

{{-- Mobile toggle script --}}
<script>
    (function () {
        const toggle   = document.getElementById('sidebarToggle');
        const sidebar  = document.querySelector('.sidebar');
        const overlay  = document.getElementById('sidebarOverlay');

        if (!toggle || !sidebar) return;

        function openSidebar() {
            sidebar.classList.add('open');
            overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeSidebar() {
            sidebar.classList.remove('open');
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        }

        toggle.addEventListener('click', () =>
            sidebar.classList.contains('open') ? closeSidebar() : openSidebar()
        );

        overlay.addEventListener('click', closeSidebar);
    })();
</script>
