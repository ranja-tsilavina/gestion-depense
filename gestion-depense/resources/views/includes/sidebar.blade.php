{{-- Sidebar partial – included once by layouts/app.blade.php --}}
<aside class="sidebar" id="sidebar">
    <a href="/dashboard" class="sidebar-brand">
        <span class="brand-icon"><i class="bi bi-wallet2 text-white"></i></span>
        DepenseApp
    </a>

    <nav>
        <a href="/dashboard" class="nav-link-custom {{ request()->is('dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        <a href="/expenses" class="nav-link-custom {{ request()->is('expenses*') ? 'active' : '' }}">
            <i class="bi bi-receipt-cutoff"></i> Dépenses
        </a>
        <a href="/revenues" class="nav-link-custom {{ request()->is('revenues*') ? 'active' : '' }}">
            <i class="bi bi-plus-circle"></i> Revenus
        </a>
        <a href="/categories" class="nav-link-custom {{ request()->is('categories*') ? 'active' : '' }}">
            <i class="bi bi-tag"></i> Catégories
        </a>
        <a href="/budgets" class="nav-link-custom {{ request()->is('budgets*') ? 'active' : '' }}">
            <i class="bi bi-wallet"></i> Budgets
        </a>

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
