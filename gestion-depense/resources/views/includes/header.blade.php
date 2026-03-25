{{-- Top header bar – included once by layouts/app.blade.php --}}
<header class="topbar" id="topbar">
    <div class="topbar-inner">

        {{-- Left: hamburger (mobile) --}}
        <div class="topbar-left">
            <button class="hamburger d-lg-none" id="sidebarToggle" aria-label="Toggle sidebar">
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
<div class="sidebar-overlay" id="sidebarOverlay"></div>
