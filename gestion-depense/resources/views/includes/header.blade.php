{{-- Top header bar – included once by layouts/app.blade.php --}}
<header class="topbar" id="topbar">
    <div class="topbar-inner">

        {{-- Left: hamburger (mobile) --}}
        <div class="topbar-left">
            <button class="hamburger d-lg-none" @click="sidebarOpen = true" aria-label="Toggle sidebar">
                <i class="bi bi-list"></i>
            </button>
        </div>

        {{-- Right: user block --}}
        <div class="topbar-right">
            <span class="topbar-greeting d-none d-md-inline">
                <i class="bi bi-sun me-1" style="color:#f59e0b"></i>
                Bonjour, <strong>{{ auth()->user()->name ?? 'Utilisateur' }}</strong>
            </span>

            <span class="topbar-divider d-none d-md-inline"></span>

            {{-- Notification Bell --}}
            <div class="notification-menu me-2" x-data="{ open: false }">
                <button
                    class="nav-icon-btn"
                    @click="open = !open"
                    @click.away="open = false"
                    style="position:relative;background:none;border:none;padding:8px;color:#64748b;font-size:1.25rem;cursor:pointer"
                >
                    <i class="bi bi-bell"></i>
                    @if(count($globalNotifications ?? []) > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:0.65rem;padding:0.35em 0.5em">
                            {{ count($globalNotifications) }}
                        </span>
                    @endif
                </button>

                <div
                    x-show="open"
                    x-transition
                    class="dropdown-menu-custom shadow-lg"
                    style="position:absolute;right:0;top:100%;z-index:1000;min-width:300px;background:white;border-radius:12px;border:1px solid #e2e8f0;margin-top:10px;padding:0;overflow:hidden"
                >
                    <div class="px-3 py-2 border-bottom d-flex justify-content-between align-items-center" style="background:#f8fafc">
                        <span style="font-weight:600;font-size:0.875rem;color:#1e293b">Notifications</span>
                        <span class="badge bg-primary-subtle text-primary" style="font-size:0.75rem">{{ count($globalNotifications ?? []) }} nouvelles</span>
                    </div>
                    <div style="max-height:300px;overflow-y:auto">
                        @forelse($globalNotifications ?? [] as $notification)
                            <div class="p-3 border-bottom notification-item" style="display:flex;gap:12px;transition:background 0.2s">
                                <div style="flex-shrink:0">
                                    <div style="width:36px;height:36px;border-radius:8px;display:flex;align-items:center;justify-content:center;background:{{ $notification['type'] == 'danger' ? '#fee2e2' : '#fef3c7' }}">
                                        <i class="bi bi-exclamation-triangle-fill" style="color:{{ $notification['type'] == 'danger' ? '#ef4444' : '#f59e0b' }}"></i>
                                    </div>
                                </div>
                                <div style="flex-grow:1">
                                    <div style="font-weight:600;font-size:0.8125rem;color:#1e293b">{{ $notification['title'] }}</div>
                                    <div style="font-size:0.75rem;color:#64748b;margin-bottom:4px">{{ $notification['message'] }}</div>
                                    <div class="progress" style="height:4px;border-radius:2px">
                                        <div class="progress-bar bg-{{ $notification['type'] }}" style="width: {{ min($notification['percent'], 100) }}%"></div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-4 text-center text-muted" style="font-size:0.875rem">
                                <i class="bi bi-bell-slash d-block mb-2" style="font-size:1.5rem"></i>
                                Aucune notification
                            </div>
                        @endforelse
                    </div>
                    <div class="p-2 border-top text-center" style="background:#f8fafc">
                        <a href="{{ route('budgets.index') }}" style="font-size:0.75rem;color:var(--primary);text-decoration:none;font-weight:600">Voir mes budgets</a>
                    </div>
                </div>
            </div>

            <span class="topbar-divider d-none d-md-inline" style="margin-right:8px"></span>

            {{-- Avatar + dropdown --}}
            <div class="user-menu dropdown">
                <button
                    class="user-avatar-btn dropdown-toggle"
                    id="userDropdown"
                    data-bs-toggle="dropdown"
                    aria-expanded="false"
                >
                    <span class="avatar-circle">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                    </span>
                    <span class="avatar-name d-none d-md-inline">{{ auth()->user()->name ?? 'Utilisateur' }}</span>
                    <i class="bi bi-chevron-down avatar-caret d-none d-md-inline"></i>
                </button>

                <ul class="dropdown-menu dropdown-menu-end user-dropdown" aria-labelledby="userDropdown">
                    <li class="dropdown-header-user">
                        <div class="dropdown-avatar-lg">
                            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                        </div>
                        <div>
                            <div class="dropdown-user-name">{{ auth()->user()->name ?? '' }}</div>
                            <div class="dropdown-user-email">{{ auth()->user()->email ?? '' }}</div>
                        </div>
                    </li>

                    <li><hr class="dropdown-divider my-1"></li>

                    <li>
                        <a class="dropdown-item dropdown-item-custom" href="/profile">
                            <i class="bi bi-person-circle"></i> Mon profil
                        </a>
                    </li>

                    <li><hr class="dropdown-divider my-1"></li>

                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item dropdown-item-custom dropdown-item-danger">
                                <i class="bi bi-box-arrow-right"></i> Déconnexion
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</header>

{{-- Sidebar overlay (mobile) --}}
<div class="sidebar-overlay" :class="{ 'active': sidebarOpen }" @click="sidebarOpen = false"></div>

