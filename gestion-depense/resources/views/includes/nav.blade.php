<nav x-data="{ mobileMenuOpen: false, profileDropdownOpen: false }" class="bg-indigo-600 shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="flex-shrink-0 flex items-center">
                    <a href="{{ url('/dashboard') }}" class="text-white font-bold text-xl tracking-wider">
                        GestionDépense
                    </a>
                </div>
                <div class="hidden sm:ml-6 sm:flex sm:space-x-4">
                    <a href="{{ url('/dashboard') }}" class="text-indigo-100 hover:bg-indigo-500 hover:text-white px-3 py-2 rounded-md text-sm font-medium {{ request()->is('dashboard') ? 'bg-indigo-700 text-white' : '' }}">Dashboard</a>
                    <a href="{{ url('/expenses') }}" class="text-indigo-100 hover:bg-indigo-500 hover:text-white px-3 py-2 rounded-md text-sm font-medium {{ request()->is('expenses*') ? 'bg-indigo-700 text-white' : '' }}">Expenses</a>
                    <a href="{{ url('/revenues') }}" class="text-indigo-100 hover:bg-indigo-500 hover:text-white px-3 py-2 rounded-md text-sm font-medium {{ request()->is('revenues*') ? 'bg-indigo-700 text-white' : '' }}">Revenues</a>
                    <a href="{{ url('/categories') }}" class="text-indigo-100 hover:bg-indigo-500 hover:text-white px-3 py-2 rounded-md text-sm font-medium {{ request()->is('categories*') ? 'bg-indigo-700 text-white' : '' }}">Categories</a>
                    <a href="{{ url('/budgets') }}" class="text-indigo-100 hover:bg-indigo-500 hover:text-white px-3 py-2 rounded-md text-sm font-medium {{ request()->is('budgets*') ? 'bg-indigo-700 text-white' : '' }}">Budgets</a>
                </div>
            </div>
            <div class="hidden sm:ml-6 sm:flex sm:items-center">
                <!-- Profile dropdown -->
                <div class="ml-3 relative">
                    <div>
                        <button @click="profileDropdownOpen = !profileDropdownOpen" @click.away="profileDropdownOpen = false" type="button" class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-indigo-300 transition duration-150 ease-in-out" id="user-menu" aria-label="User menu" aria-haspopup="true">
                            <span class="inline-block h-8 w-8 rounded-full bg-indigo-200 overflow-hidden flex justify-center items-center">
                                <svg class="h-full w-full text-indigo-500" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </span>
                        </button>
                    </div>
                    <div x-show="profileDropdownOpen" x-cloak class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg z-50">
                        <div class="py-1 rounded-md bg-white shadow-xs" role="menu" aria-orientation="vertical" aria-labelledby="user-menu">
                            <a href="{{ url('/profile') }}" class="block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out" role="menuitem">Profile</a>
                            <form method="POST" action="{{ route('logout') ?? url('/logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out" role="menuitem">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="-mr-2 flex items-center sm:hidden">
                <!-- Mobile menu button -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="inline-flex items-center justify-center p-2 rounded-md text-indigo-200 hover:text-white hover:bg-indigo-500 focus:outline-none focus:bg-indigo-500 focus:text-white transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': mobileMenuOpen, 'inline-flex': !mobileMenuOpen }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': !mobileMenuOpen, 'inline-flex': mobileMenuOpen }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div x-show="mobileMenuOpen" x-cloak class="sm:hidden">
        <div class="px-2 pt-2 pb-3 space-y-1">
            <a href="{{ url('/dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium text-white bg-indigo-700">Dashboard</a>
            <a href="{{ url('/expenses') }}" class="block px-3 py-2 rounded-md text-base font-medium text-indigo-100 hover:text-white hover:bg-indigo-500">Expenses</a>
            <a href="{{ url('/revenues') }}" class="block px-3 py-2 rounded-md text-base font-medium text-indigo-100 hover:text-white hover:bg-indigo-500">Revenues</a>
            <a href="{{ url('/categories') }}" class="block px-3 py-2 rounded-md text-base font-medium text-indigo-100 hover:text-white hover:bg-indigo-500">Categories</a>
            <a href="{{ url('/budgets') }}" class="block px-3 py-2 rounded-md text-base font-medium text-indigo-100 hover:text-white hover:bg-indigo-500">Budgets</a>
        </div>
        <div class="pt-4 pb-3 border-t border-indigo-500">
            <div class="flex items-center px-5">
                <div class="flex-shrink-0">
                    <span class="inline-block h-10 w-10 rounded-full bg-indigo-200 overflow-hidden flex justify-center items-center">
                        <svg class="h-full w-full text-indigo-500" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </span>
                </div>
                <div class="ml-3">
                    <div class="text-base font-medium leading-none text-white">{{ auth()->user()->name ?? 'User' }}</div>
                    <div class="mt-1 text-sm font-medium leading-none text-indigo-200">{{ auth()->user()->email ?? 'user@example.com' }}</div>
                </div>
            </div>
            <div class="mt-3 px-2 space-y-1">
                <a href="{{ url('/profile') }}" class="block px-3 py-2 rounded-md text-base font-medium text-indigo-100 hover:text-white hover:bg-indigo-500">Profile</a>
                <form method="POST" action="{{ route('logout') ?? url('/logout') }}">
                    @csrf
                    <button type="submit" class="block w-full text-left px-3 py-2 rounded-md text-base font-medium text-indigo-100 hover:text-white hover:bg-indigo-500">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
