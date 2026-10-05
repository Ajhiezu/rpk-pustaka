<nav x-data="{ open: false }" class="bg-white border-b border-slate-100 sticky top-0 z-40">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center pr-8 border-r border-slate-50">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-100">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        </div>
                        <span class="text-xl font-black tracking-tighter text-slate-900 italic">Tech<span class="text-indigo-600 not-italic">Perpus</span></span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-6 sm:-my-px sm:ms-8 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-xs font-bold uppercase tracking-widest transition-all">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    @if(Auth::user()->isAdmin())
                        <x-nav-link :href="route('admin.books.index')" :active="request()->routeIs('admin.books.*')" class="text-xs font-bold uppercase tracking-widest">
                            {{ __('Buku') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.categories.index')" :active="request()->routeIs('admin.categories.*')" class="text-xs font-bold uppercase tracking-widest">
                            {{ __('Kategori') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.locations.index')" :active="request()->routeIs('admin.locations.*')" class="text-xs font-bold uppercase tracking-widest">
                            {{ __('Rak') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.loans.index')" :active="request()->routeIs('admin.loans.*')" class="text-xs font-bold uppercase tracking-widest">
                            {{ __('Pinjaman') }}
                        </x-nav-link>
                    @endif

                    @if(Auth::user()->isAnggota())
                        <x-nav-link :href="route('anggota.books.index')" :active="request()->routeIs('anggota.books.*', 'member.books.*')" class="text-xs font-bold uppercase tracking-widest">
                            {{ __('Katalog Buku') }}
                        </x-nav-link>
                        <x-nav-link :href="route('anggota.loans.index')" :active="request()->routeIs('anggota.loans.*', 'member.loans.*')" class="text-xs font-bold uppercase tracking-widest">
                            {{ __('Status Pinjam') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-4 py-2 border border-slate-100 text-sm leading-4 font-bold rounded-xl text-slate-600 bg-slate-50 hover:bg-white hover:text-slate-900 focus:outline-none transition ease-in-out duration-150 shadow-sm">
                            <div class="mr-2 w-6 h-6 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center text-[10px]">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4 opacity-40" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')" class="font-bold text-xs uppercase tracking-widest py-3">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    class="font-bold text-xs uppercase tracking-widest py-3 text-rose-500"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-xl text-slate-400 hover:text-slate-500 hover:bg-slate-100 focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t border-slate-50">
        <div class="pt-2 pb-3 space-y-1 px-4">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="rounded-xl font-bold uppercase text-xs tracking-widest">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <!-- Add other links here if needed for mobile -->
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-slate-100 px-4 mb-4">
            <div class="px-4 py-3 bg-slate-50 rounded-2xl flex items-center mb-4">
                <div class="w-10 h-10 bg-indigo-600 rounded-full flex items-center justify-center text-white font-bold mr-3 shadow-lg shadow-indigo-100">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div>
                    <div class="font-bold text-sm text-slate-900">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-xs text-slate-400">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')" class="rounded-xl font-bold uppercase text-xs tracking-widest">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            class="rounded-xl font-bold uppercase text-xs tracking-widest text-rose-500"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
