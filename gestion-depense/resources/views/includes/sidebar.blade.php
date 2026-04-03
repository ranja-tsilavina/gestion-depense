{{-- Sidebar partial – included once by layouts/app.blade.php --}}
<aside class="sidebar" id="sidebar" :class="{ 'open': sidebarOpen }">
    <a href="/dashboard" class="sidebar-brand">
        <span class="brand-icon"><i class="bi bi-wallet2 text-white"></i></span>
        FintechApp
    </a>
    <div class="px-3 mb-3 text-center">
        <small class="text-white-50 text-uppercase fw-bold" style="font-size:0.65rem">Ma Maison Actuelle</small>
        <div class="text-white small fw-bold">{{ App\Models\Household::find(session('active_household_id'))->name ?? 'Sans Maison' }}</div>
    </div>

    <nav>
        <a href="/dashboard" class="nav-link-custom {{ request()->is('dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        <a href="/expenses" class="nav-link-custom {{ request()->is('expenses*') ? 'active' : '' }}">
            <i class="bi bi-receipt-cutoff"></i> Dépenses
        </a>
        @if(auth()->user()->isOwner())
        <a href="/revenues" class="nav-link-custom {{ request()->is('revenues*') ? 'active' : '' }}">
            <i class="bi bi-plus-circle"></i> Revenus
        </a>
        <a href="/categories" class="nav-link-custom {{ request()->is('categories*') ? 'active' : '' }}">
            <i class="bi bi-tag"></i> Catégories
        </a>
        @endif
        <a href="/budgets" class="nav-link-custom {{ request()->is('budgets*') ? 'active' : '' }}">
            <i class="bi bi-wallet"></i> Budgets
        </a>

        @if(auth()->user()->isOwner())
        <div style="border-top:1px solid rgba(255,255,255,.12);margin:0.5rem 0"></div>
        <small class="text-white-50 px-3 py-2 d-block text-uppercase fw-bold" style="font-size:0.65rem">Système Financier</small>

        <a href="/accounts" class="nav-link-custom {{ request()->is('accounts*') ? 'active' : '' }}">
            <i class="bi bi-bank text-info"></i> Comptes
        </a>
        <a href="/transfers" class="nav-link-custom {{ request()->is('transfers*') ? 'active' : '' }}">
            <i class="bi bi-arrow-left-right text-success"></i> Transferts
        </a>

        <div style="border-top:1px solid rgba(255,255,255,.12);margin:0.5rem 0"></div>
        <small class="text-white-50 px-3 py-2 d-block text-uppercase fw-bold" style="font-size:0.65rem">Journal</small>

        <a href="{{ route('activities.index') }}" class="nav-link-custom {{ request()->is('activities*') ? 'active' : '' }}">
            <i class="bi bi-activity text-warning"></i> Journal d'Activité
        </a>
        @endif

        @if(auth()->user()->isOwner())
        <div style="border-top:1px solid rgba(255,255,255,.12);margin:0.5rem 0"></div>
        <small class="text-white-50 px-3 py-2 d-block text-uppercase fw-bold" style="font-size:0.65rem">Administration</small>

        <a href="{{ route('households.members') }}" class="nav-link-custom {{ request()->is('households/members*') ? 'active' : '' }}">
            <i class="bi bi-people-fill" style="color:#f472b6"></i> Gestion Famille
        </a>
        <a href="/households" class="nav-link-custom {{ request()->is('households') ? 'active' : '' }}">
            <i class="bi bi-house-door text-warning"></i> Ma Maison
        </a>
        @endif

        <div style="border-top:1px solid rgba(255,255,255,.12);margin:1rem 0"></div>

        <a href="/profile" class="nav-link-custom {{ request()->is('profile*') ? 'active' : '' }}">
            <i class="bi bi-person-circle"></i> Profil
        </a>
        <form method="POST" action="{{ route('logout') }}" class="m-0">
            @csrf
            <button type="submit" class="nav-link-custom w-100 border-0 bg-transparent text-start" style="cursor:pointer">
                <i class="bi bi-box-arrow-left"></i> Déconnexion
            </button>
        </form>
    </nav>
</aside>
