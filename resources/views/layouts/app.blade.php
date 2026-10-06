<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'RPK PUSTAKA IMM SAINTEKMU') }} — Modern Academic Editorial Library</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('images/logo-rpk.ico') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/logo-rpk.ico') }}">

    <!-- Google Fonts: Inter Sans-Serif System -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] {
            display: none !important;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: #FFFFFF;
            color: #181818;
        }

        .font-serif, h1, h2, h3, h4, h5, h6 {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }
    </style>
</head>

<body class="font-sans antialiased text-neutral-dark bg-white selection:bg-primary/10 selection:text-primary">
    <div class="min-h-screen flex" 
         x-data="{ 
             sidebarOpen: window.innerWidth >= 1024, 
             mobileSidebarOpen: false,
             isDesktop: window.innerWidth >= 1024,
             toggleSidebar() {
                 if (!this.isDesktop) {
                     this.mobileSidebarOpen = !this.mobileSidebarOpen;
                     if (this.mobileSidebarOpen) {
                         this.sidebarOpen = true;
                     }
                 } else {
                     this.sidebarOpen = !this.sidebarOpen;
                 }
             },
             closeMobileSidebar() {
                 this.mobileSidebarOpen = false;
             }
         }"
         @resize.window="isDesktop = window.innerWidth >= 1024; if (isDesktop) { mobileSidebarOpen = false }">

        <!-- Mobile Backdrop -->
        <div x-show="mobileSidebarOpen" x-cloak 
             @click="closeMobileSidebar()" 
             class="fixed inset-0 bg-[#181818]/40 z-40 lg:hidden backdrop-blur-xs transition-opacity"></div>

        <!-- Sidebar: Fixed Desktop, Off-canvas Mobile -->
        <aside
            x-show="isDesktop || mobileSidebarOpen"
            x-cloak
            class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-neutral-border transition-all duration-300 flex flex-col shadow-xs overflow-hidden lg:translate-x-0"
            :class="{
                'lg:w-64': sidebarOpen,
                'lg:w-20': !sidebarOpen
            }">
            
            <!-- Brand masthead area with official RPK PUSTAKA IMM SAINTEKMU logo -->
            <div class="h-20 flex items-center justify-between px-4 border-b border-neutral-border flex-shrink-0 bg-white">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 min-w-0 group">
                    <img src="{{ asset('images/logo-rpk.png') }}" alt="RPK PUSTAKA IMM SAINTEKMU" class="h-10 w-auto object-contain shrink-0">
                    <div class="transition-all duration-300 overflow-hidden whitespace-nowrap"
                         :class="sidebarOpen ? 'opacity-100' : 'opacity-0 -translate-x-6 w-0'">
                        <span class="font-sans text-base font-bold tracking-tight text-neutral-dark block leading-none">RPK PUSTAKA</span>
                        <span class="text-[10px] font-semibold text-neutral-muted uppercase tracking-wider block mt-0.5 whitespace-nowrap">IMM SAINTEKMU</span>
                    </div>
                </a>
                <button type="button"
                        @click="closeMobileSidebar()"
                        class="lg:hidden shrink-0 p-1.5 rounded-md text-neutral-muted hover:text-primary hover:bg-primary-light transition-colors cursor-pointer"
                        title="Tutup Menu Mobile">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Navigation Sidebar Links -->
            <nav class="flex-1 px-3 py-4 space-y-1.5 overflow-y-auto custom-scrollbar">

                <div class="px-3 pt-2 pb-1 text-[10px] font-bold text-neutral-muted uppercase tracking-[0.2em]"
                    x-show="sidebarOpen">Navigasi Utama</div>

                <a href="{{ route('dashboard') }}"
                    class="sidebar-link {{ request()->routeIs('dashboard') ? 'sidebar-active' : 'sidebar-inactive' }}"
                    x-data="{ tooltip: false }" @mouseenter="!sidebarOpen ? tooltip = true : null"
                    @mouseleave="tooltip = false">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                        </path>
                    </svg>
                    <span class="ml-3 transition-all duration-300 whitespace-nowrap"
                        :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">Dashboard</span>

                    <div x-show="tooltip" x-cloak
                        class="absolute left-full ml-3 px-2.5 py-1 bg-neutral-dark text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">
                        Dashboard</div>
                </a>

                <a href="{{ route('activities.index') }}"
                    class="sidebar-link {{ request()->routeIs('activities.index') ? 'sidebar-active' : 'sidebar-inactive' }}"
                    x-data="{ tooltip: false }" @mouseenter="!sidebarOpen ? tooltip = true : null"
                    @mouseleave="tooltip = false">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="ml-3 transition-all duration-300 whitespace-nowrap"
                        :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">Aktivitas</span>

                    <div x-show="tooltip" x-cloak
                        class="absolute left-full ml-3 px-2.5 py-1 bg-neutral-dark text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">
                        Aktivitas & Log</div>
                </a>

                @auth
                    @if(Auth::user()->isAnggota())
                        <div class="px-3 pt-5 pb-1 text-[10px] font-bold text-neutral-muted uppercase tracking-[0.2em]"
                            x-show="sidebarOpen">Layanan Anggota</div>

                        <a href="{{ route('anggota.books.index') }}"
                            class="sidebar-link {{ request()->routeIs('*.books.index') || request()->routeIs('*.books.show') ? 'sidebar-active' : 'sidebar-inactive' }}"
                            x-data="{ tooltip: false }" @mouseenter="!sidebarOpen ? tooltip = true : null"
                            @mouseleave="tooltip = false">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                </path>
                            </svg>
                            <span class="ml-3 transition-all duration-300 whitespace-nowrap"
                                :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">Katalog Buku</span>

                            <div x-show="tooltip" x-cloak
                                class="absolute left-full ml-3 px-2.5 py-1 bg-neutral-dark text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">
                                Jelajahi Katalog</div>
                        </a>

                        <a href="{{ route('anggota.loans.index') }}"
                            class="sidebar-link {{ request()->routeIs('*.loans.*') ? 'sidebar-active' : 'sidebar-inactive' }}"
                            x-data="{ tooltip: false }" @mouseenter="!sidebarOpen ? tooltip = true : null"
                            @mouseleave="tooltip = false">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                            <span class="ml-3 transition-all duration-300 whitespace-nowrap"
                                :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">Pinjaman Saya</span>

                            <div x-show="tooltip" x-cloak
                                class="absolute left-full ml-3 px-2.5 py-1 bg-neutral-dark text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">
                                Riwayat Pinjaman</div>
                        </a>

                        <a href="{{ route('anggota.articles.index') }}"
                            class="sidebar-link {{ request()->routeIs('*.articles.*') ? 'sidebar-active' : 'sidebar-inactive' }}"
                            x-data="{ tooltip: false }" @mouseenter="!sidebarOpen ? tooltip = true : null"
                            @mouseleave="tooltip = false">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z">
                                </path>
                            </svg>
                            <span class="ml-3 transition-all duration-300 whitespace-nowrap"
                                :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">Artikel Pustaka</span>

                            <div x-show="tooltip" x-cloak
                                class="absolute left-full ml-3 px-2.5 py-1 bg-neutral-dark text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">
                                Artikel & Wawasan</div>
                        </a>

                        <a href="{{ route('anggota.essays.index') }}"
                            class="sidebar-link {{ request()->routeIs('*.essays.*') ? 'sidebar-active' : 'sidebar-inactive' }}"
                            x-data="{ tooltip: false }" @mouseenter="!sidebarOpen ? tooltip = true : null"
                            @mouseleave="tooltip = false">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                </path>
                            </svg>
                            <span class="ml-3 transition-all duration-300 whitespace-nowrap"
                                :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">Tulisan Saya</span>

                            <div x-show="tooltip" x-cloak
                                class="absolute left-full ml-3 px-2.5 py-1 bg-neutral-dark text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">
                                Esai & Karya Tulis</div>
                        </a>
                    @endif

                    @if(Auth::user()->isAdmin())
                        <div class="px-3 pt-5 pb-1 text-[10px] font-bold text-neutral-muted uppercase tracking-[0.2em]"
                            x-show="sidebarOpen">Katalog & Rak</div>

                        <a href="{{ route('admin.books.index') }}"
                            class="sidebar-link {{ request()->routeIs('admin.books.*') ? 'sidebar-active' : 'sidebar-inactive' }}"
                            x-data="{ tooltip: false }" @mouseenter="!sidebarOpen ? tooltip = true : null"
                            @mouseleave="tooltip = false">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                </path>
                            </svg>
                            <span class="ml-3 transition-all duration-300 whitespace-nowrap"
                                :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">Data Buku</span>

                            <div x-show="tooltip" x-cloak
                                class="absolute left-full ml-3 px-2.5 py-1 bg-neutral-dark text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">
                                Koleksi Buku</div>
                        </a>

                        <a href="{{ route('admin.categories.index') }}"
                            class="sidebar-link {{ request()->routeIs('admin.categories.*') ? 'sidebar-active' : 'sidebar-inactive' }}"
                            x-data="{ tooltip: false }" @mouseenter="!sidebarOpen ? tooltip = true : null"
                            @mouseleave="tooltip = false">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                                </path>
                            </svg>
                            <span class="ml-3 transition-all duration-300 whitespace-nowrap"
                                :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">Kategori</span>

                            <div x-show="tooltip" x-cloak
                                class="absolute left-full ml-3 px-2.5 py-1 bg-neutral-dark text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">
                                Kategori Buku</div>
                        </a>

                        <a href="{{ route('admin.locations.index') }}"
                            class="sidebar-link {{ request()->routeIs('admin.locations.*') ? 'sidebar-active' : 'sidebar-inactive' }}"
                            x-data="{ tooltip: false }" @mouseenter="!sidebarOpen ? tooltip = true : null"
                            @mouseleave="tooltip = false">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                </path>
                            </svg>
                            <span class="ml-3 transition-all duration-300 whitespace-nowrap"
                                :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">Lokasi Rak</span>

                            <div x-show="tooltip" x-cloak
                                class="absolute left-full ml-3 px-2.5 py-1 bg-neutral-dark text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">
                                Rak Penyimpanan</div>
                        </a>

                        <div class="px-3 pt-5 pb-1 text-[10px] font-bold text-neutral-muted uppercase tracking-[0.2em]"
                            x-show="sidebarOpen">Sirkulasi & Layanan</div>

                        <a href="{{ route('admin.loans.index') }}"
                            class="sidebar-link {{ request()->routeIs('admin.loans.*') ? 'sidebar-active' : 'sidebar-inactive' }}"
                            x-data="{ tooltip: false }" @mouseenter="!sidebarOpen ? tooltip = true : null"
                            @mouseleave="tooltip = false">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                            <span class="ml-3 transition-all duration-300 whitespace-nowrap"
                                :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">Peminjaman</span>

                            <div x-show="tooltip" x-cloak
                                class="absolute left-full ml-3 px-2.5 py-1 bg-neutral-dark text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">
                                Transaksi Sirkulasi</div>
                        </a>

                        <a href="{{ route('admin.articles.index') }}"
                            class="sidebar-link {{ request()->routeIs('admin.articles.*') ? 'sidebar-active' : 'sidebar-inactive' }}"
                            x-data="{ tooltip: false }" @mouseenter="!sidebarOpen ? tooltip = true : null"
                            @mouseleave="tooltip = false">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z">
                                </path>
                            </svg>
                            <span class="ml-3 transition-all duration-300 whitespace-nowrap"
                                :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">Kelola Artikel</span>

                            <div x-show="tooltip" x-cloak
                                class="absolute left-full ml-3 px-2.5 py-1 bg-neutral-dark text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">
                                Manajemen Konten Artikel</div>
                        </a>

                        <a href="{{ route('admin.essays.index') }}"
                            class="sidebar-link {{ request()->routeIs('admin.essays.*') ? 'sidebar-active' : 'sidebar-inactive' }}"
                            x-data="{ tooltip: false }" @mouseenter="!sidebarOpen ? tooltip = true : null"
                            @mouseleave="tooltip = false">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            <span class="ml-3 transition-all duration-300 whitespace-nowrap"
                                :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">Kurasi Esai</span>

                            <div x-show="tooltip" x-cloak
                                class="absolute left-full ml-3 px-2.5 py-1 bg-neutral-dark text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">
                                Review Esai Anggota</div>
                        </a>

                        <a href="{{ route('admin.reports.index') }}"
                            class="sidebar-link {{ request()->routeIs('admin.reports.*') ? 'sidebar-active' : 'sidebar-inactive' }}"
                            x-data="{ tooltip: false }" @mouseenter="!sidebarOpen ? tooltip = true : null"
                            @mouseleave="tooltip = false">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            <span class="ml-3 transition-all duration-300 whitespace-nowrap"
                                :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">Laporan</span>

                            <div x-show="tooltip" x-cloak
                                class="absolute left-full ml-3 px-2.5 py-1 bg-neutral-dark text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">
                                Rekapitulasi & Arsip</div>
                        </a>

                        <div class="px-3 pt-5 pb-1 text-[10px] font-bold text-neutral-muted uppercase tracking-[0.2em]"
                            x-show="sidebarOpen">Pengguna & Akses</div>

                        <a href="{{ route('admin.users.index') }}"
                            class="sidebar-link {{ (request()->routeIs('admin.users.*') || request()->routeIs('admin.members.import.*')) ? 'sidebar-active' : 'sidebar-inactive' }}"
                            x-data="{ tooltip: false }" @mouseenter="!sidebarOpen ? tooltip = true : null"
                            @mouseleave="tooltip = false">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                </path>
                            </svg>
                            <span class="ml-3 transition-all duration-300 whitespace-nowrap"
                                :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">Anggota</span>
                            <div x-show="tooltip"
                                class="absolute left-full ml-3 px-2.5 py-1 bg-neutral-dark text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">
                                Kelola Data Anggota</div>
                        </a>

                        <a href="{{ route('admin.settings.index') }}"
                            class="sidebar-link {{ request()->routeIs('admin.settings.*') ? 'sidebar-active' : 'sidebar-inactive' }}"
                            x-data="{ tooltip: false }" @mouseenter="!sidebarOpen ? tooltip = true : null"
                            @mouseleave="tooltip = false">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span class="ml-3 transition-all duration-300 whitespace-nowrap"
                                :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">Pengaturan</span>

                            <div x-show="tooltip" x-cloak
                                class="absolute left-full ml-3 px-2.5 py-1 bg-neutral-dark text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">
                                Konfigurasi Sistem</div>
                        </a>
                    @endif
                @endauth

                <div class="px-3 pt-5 pb-1 text-[10px] font-bold text-neutral-muted uppercase tracking-[0.2em]"
                    x-show="sidebarOpen">Laman Publik</div>

                <a href="{{ url('/') }}" class="sidebar-link sidebar-inactive" x-data="{ tooltip: false }"
                    @mouseenter="!sidebarOpen ? tooltip = true : null" @mouseleave="tooltip = false">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9">
                        </path>
                    </svg>
                    <span class="ml-3 transition-all duration-300 whitespace-nowrap"
                        :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">Katalog Publik</span>

                    <div x-show="tooltip" x-cloak
                        class="absolute left-full ml-3 px-2.5 py-1 bg-neutral-dark text-white text-xs rounded shadow-lg z-50 whitespace-nowrap">
                        Halaman Depan</div>
                </a>
            </nav>

            <!-- Bottom Sidebar Profile / Logout -->
            <div class="p-3 border-t border-neutral-border bg-white flex-shrink-0">
                @auth
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="flex items-center w-full px-3 py-2 text-xs font-semibold text-primary hover:bg-primary-light rounded-md transition-colors group overflow-hidden cursor-pointer">
                            <svg class="w-4 h-4 flex-shrink-0 group-hover:translate-x-0.5 transition-transform" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                </path>
                            </svg>
                            <span class="ml-3 transition-all duration-300 whitespace-nowrap"
                                :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'">Keluar Sistem</span>
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn-editorial w-full text-xs py-2">
                        Masuk Akun
                    </a>
                @endauth
            </div>
        </aside>

        <!-- Main Content Canvas -->
        <div class="flex-1 min-w-0 transition-all duration-300 flex flex-col min-h-screen bg-white" :class="sidebarOpen ? 'lg:pl-64' : 'lg:pl-20'">
            <!-- Topbar -->
            <header
                class="h-18 bg-white/95 backdrop-blur-md border-b border-neutral-border sticky top-0 z-40 flex items-center justify-between px-6 lg:px-10">
                <div class="flex items-center space-x-4">
                    <button @click="toggleSidebar()"
                        class="w-9 h-9 flex items-center justify-center hover:bg-neutral-surface rounded-md transition-colors text-neutral-dark border border-neutral-border cursor-pointer"
                        title="Alihkan Sidebar">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>

                    <div class="hidden sm:flex items-center gap-2">
                        <span class="font-sans text-sm tracking-wide uppercase font-semibold text-neutral-dark">RPK PUSTAKA</span>
                        <span class="text-neutral-muted text-xs">•</span>
                        <span class="text-xs text-neutral-body">IMM SAINTEKMU</span>
                    </div>
                </div>

                <div class="flex items-center space-x-5">
                    @auth
                        <div class="flex items-center space-x-3 pr-4 border-r border-neutral-border">
                            <div class="text-right hidden sm:block">
                                <p class="text-xs font-semibold text-neutral-dark leading-tight">{{ Auth::user()->name }}</p>
                                <span class="inline-block mt-0.5 px-2 py-0.2 bg-primary-light text-[10px] font-bold text-primary uppercase tracking-wider rounded border border-red-200">{{ Auth::user()->role }}</span>
                            </div>
                            <div
                                class="w-9 h-9 bg-primary-light text-primary font-sans font-bold text-sm rounded-md flex items-center justify-center border border-red-200 uppercase shadow-xs">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                        </div>

                        <a href="{{ route('profile.edit') }}"
                            class="w-9 h-9 flex items-center justify-center hover:bg-neutral-surface rounded-md transition-colors text-neutral-body hover:text-primary border border-transparent hover:border-neutral-border"
                            title="Pengaturan Profil">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </a>
                    @else
                        <div class="flex items-center space-x-3">
                            <a href="{{ route('login') }}" class="text-xs font-semibold text-neutral-dark hover:text-primary transition-colors">Masuk</a>
                            <a href="{{ route('register') }}" class="btn-editorial text-xs py-1.5 px-3.5">Daftar</a>
                        </div>
                    @endauth
                </div>
            </header>

            <!-- Page Content Area -->
            <main class="flex-1 px-6 lg:px-12 py-10 max-w-7xl w-full mx-auto">
                @isset($header)
                    <div class="mb-8 pb-6 border-b border-neutral-border flex flex-col md:flex-row md:items-baseline md:justify-between gap-4">
                        <div>
                            <h1 class="font-sans text-2xl sm:text-3xl font-bold text-neutral-dark tracking-tight">{{ $header }}</h1>
                            <p class="text-xs text-neutral-muted font-normal mt-1.5 uppercase tracking-wider">RPK PUSTAKA IMM SAINTEKMU — Sistem Perpustakaan & Arsip Akademik</p>
                        </div>

                        @isset($actions)
                            <div class="flex items-center space-x-3">
                                {{ $actions }}
                            </div>
                        @endisset
                    </div>
                @endisset

                <div class="animate-in fade-in duration-300">
                    {{ $slot }}
                </div>
            </main>

            <!-- Refined Academic Footer -->
            <footer class="mt-auto border-t border-neutral-border bg-white px-6 lg:px-12 py-6 text-center text-xs text-neutral-muted">
                <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2">
                    <p>&copy; {{ date('Y') }} RPK PUSTAKA IMM SAINTEKMU — Hak Cipta Dilindungi. Perpustakaan Riset & Akademik.</p>
                    <div class="flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-accent"></span>
                        <p class="font-sans italic text-neutral-body">Veritas et Sapientia</p>
                    </div>
                </div>
            </footer>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Global SweetAlert Delete Confirmation helper
        window.confirmDelete = function (target, message = 'Data yang dihapus tidak dapat dipulihkan kembali. Lanjutkan proses penghapusan?', title = 'Konfirmasi Hapus Data') {
            let form = null;
            if (target && target.preventDefault) {
                target.preventDefault();
                form = target.target ? target.target.closest('form') : target;
            } else if (target instanceof HTMLFormElement) {
                form = target;
            } else if (target && target.closest) {
                form = target.closest('form');
            }

            Swal.fire({
                title: title,
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#C62828',
                cancelButtonColor: '#666666',
                confirmButtonText: 'Ya, Hapus Data',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                background: '#FFFFFF',
                focusCancel: true,
                customClass: {
                    popup: 'rounded-xl border border-[#E5E5E5] shadow-2xl font-sans text-neutral-dark',
                    title: 'font-sans font-bold text-neutral-dark text-lg pt-2',
                    htmlContainer: 'font-sans text-xs sm:text-sm text-[#666666] leading-relaxed',
                    confirmButton: 'rounded-md px-5 py-2.5 font-sans font-semibold text-xs uppercase tracking-wider shadow-sm cursor-pointer ml-2',
                    cancelButton: 'rounded-md px-5 py-2.5 font-sans font-semibold text-xs uppercase tracking-wider shadow-sm cursor-pointer mr-2'
                }
            }).then((result) => {
                if (result.isConfirmed && form) {
                    form.dataset.confirmed = 'true';
                    form.submit();
                }
            });

            return false;
        };

        // ==========================================
        // Live Instant Search & Filter Engine
        // ==========================================
        let liveSearchTimer = null;
        let liveSearchAbortController = null;

        window.performLiveSearch = async function (form) {
            if (!form) return;

            if (liveSearchAbortController) {
                liveSearchAbortController.abort();
            }
            liveSearchAbortController = new AbortController();

            const formData = new FormData(form);
            const searchParams = new URLSearchParams();
            for (const [key, value] of formData.entries()) {
                if (value !== null && value.toString().trim() !== '') {
                    searchParams.append(key, value.toString().trim());
                }
            }

            const actionUrl = new URL(form.action || window.location.href, window.location.origin);
            actionUrl.search = searchParams.toString();

            const activeEl = document.activeElement;
            const activeName = activeEl ? activeEl.getAttribute('name') : null;
            const activeSelectionStart = (activeEl && 'selectionStart' in activeEl) ? activeEl.selectionStart : null;
            const activeSelectionEnd = (activeEl && 'selectionEnd' in activeEl) ? activeEl.selectionEnd : null;

            const currentMain = document.querySelector('main');
            const currentToolbar = form.closest('.bg-white, .p-4, .p-5, .rounded-lg');
            const targetResults = currentToolbar ? currentToolbar.parentElement : currentMain;

            if (targetResults) {
                targetResults.style.transition = 'opacity 0.15s ease';
                targetResults.style.opacity = '0.55';
            }

            try {
                const response = await fetch(actionUrl.toString(), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    signal: liveSearchAbortController.signal
                });

                if (!response.ok) {
                    form.submit();
                    return;
                }

                const html = await response.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newMain = doc.querySelector('main');

                if (currentMain && newMain) {
                    const currentForm = currentMain.querySelector('form[method="GET"]');
                    const newForm = newMain.querySelector('form[method="GET"]');

                    // Sync Reset button inside currentForm
                    if (currentForm && newForm) {
                        const currentReset = currentForm.querySelector('a.btn-editorial-outline');
                        const newReset = newForm.querySelector('a.btn-editorial-outline');
                        if (currentReset && newReset) {
                            currentReset.href = newReset.href;
                        } else if (!currentReset && newReset) {
                            const btnContainer = currentForm.querySelector('.flex.space-x-2, .space-x-2');
                            if (btnContainer) btnContainer.appendChild(newReset.cloneNode(true));
                        } else if (currentReset && !newReset) {
                            currentReset.remove();
                        }

                        // Sync format pills href inside toolbar if present
                        const curPills = currentForm.closest('.bg-white, .p-4, .p-5')?.querySelectorAll('a[href*="admin/books"], a[href*="anggota/books"]');
                        const newPills = newForm.closest('.bg-white, .p-4, .p-5')?.querySelectorAll('a[href*="admin/books"], a[href*="anggota/books"]');
                        if (curPills && newPills && curPills.length === newPills.length) {
                            curPills.forEach((pill, idx) => {
                                pill.href = newPills[idx].href;
                                pill.className = newPills[idx].className;
                            });
                        }
                    }

                    // Replace everything after the toolbar
                    const curToolbar = currentForm ? currentForm.closest('.bg-white, .p-4, .p-5, .rounded-lg') : null;
                    const nToolbar = newForm ? newForm.closest('.bg-white, .p-4, .p-5, .rounded-lg') : null;

                    if (curToolbar && nToolbar && curToolbar.parentElement && nToolbar.parentElement) {
                        const parent = curToolbar.parentElement;
                        const newParent = nToolbar.parentElement;

                        while (curToolbar.nextElementSibling) {
                            curToolbar.nextElementSibling.remove();
                        }

                        let nextNode = nToolbar.nextElementSibling;
                        while (nextNode) {
                            parent.appendChild(nextNode.cloneNode(true));
                            nextNode = nextNode.nextElementSibling;
                        }
                    } else {
                        currentMain.innerHTML = newMain.innerHTML;
                    }

                    // Restore focus and cursor if input is active
                    if (activeName) {
                        const restored = document.querySelector(`input[name="${activeName}"], select[name="${activeName}"]`);
                        if (restored && document.activeElement !== restored) {
                            restored.focus();
                            if (activeSelectionStart !== null && 'setSelectionRange' in restored) {
                                try {
                                    restored.setSelectionRange(activeSelectionStart, activeSelectionEnd);
                                } catch (e) {}
                            }
                        }
                    }

                    if (window.Alpine) {
                        window.Alpine.initTree(currentMain);
                    }

                    window.history.replaceState(null, '', actionUrl.toString());
                } else {
                    form.submit();
                }
            } catch (err) {
                if (err.name !== 'AbortError') {
                    form.submit();
                }
            } finally {
                if (targetResults) {
                    targetResults.style.opacity = '1';
                }
            }
        };

        document.addEventListener('DOMContentLoaded', function () {
            // Live typing listener on search inputs
            document.addEventListener('input', function (e) {
                const input = e.target;
                if (input && (input.name === 'search' || input.type === 'search')) {
                    const form = input.closest('form[method="GET"]');
                    if (form) {
                        clearTimeout(liveSearchTimer);
                        liveSearchTimer = setTimeout(() => {
                            window.performLiveSearch(form);
                        }, 300);
                    }
                }
            });

            // Live filter on dropdown selection changes
            document.addEventListener('change', function (e) {
                const select = e.target;
                if (select && select.tagName === 'SELECT') {
                    const form = select.closest('form');
                    if (form && (form.method.toUpperCase() === 'GET' || !form.method)) {
                        if (select.name === 'role_filter' && form.role) {
                            form.role.value = select.value;
                        }
                        clearTimeout(liveSearchTimer);
                        window.performLiveSearch(form);
                    }
                }
            });

            // Handle immediate search on Enter or button click
            document.addEventListener('submit', function (e) {
                const form = e.target;
                if (form && form.method.toUpperCase() === 'GET' && form.querySelector('input[name="search"], input[type="search"]')) {
                    e.preventDefault();
                    clearTimeout(liveSearchTimer);
                    window.performLiveSearch(form);
                }
            });

            // Intercept all DELETE forms globally across the system
            document.addEventListener('submit', function (e) {
                const form = e.target;
                if (!form || form.dataset.confirmed === 'true') {
                    return;
                }

                const methodInput = form.querySelector('input[name="_method"]');
                const isDelete = (methodInput && methodInput.value.toUpperCase() === 'DELETE') || form.classList.contains('delete-form');

                if (isDelete && !form.hasAttribute('data-no-confirm') && !form.getAttribute('action')?.includes('profile')) {
                    e.preventDefault();
                    e.stopPropagation();
                    const customMessage = form.getAttribute('data-confirm-message') || 'Data yang dihapus tidak dapat dipulihkan kembali. Lanjutkan proses penghapusan?';
                    const customTitle = form.getAttribute('data-confirm-title') || 'Konfirmasi Hapus Data';
                    window.confirmDelete(form, customMessage, customTitle);
                }
            }, true);

            // Intercept all LOAN borrowing submission forms with SweetAlert2 confirmation
            document.addEventListener('submit', function (e) {
                const form = e.target;
                if (!form || form.dataset.confirmed === 'true') {
                    return;
                }

                // Handle custom confirmation forms (like fine payment, etc.)
                if (form.hasAttribute('data-confirm-message')) {
                    e.preventDefault();
                    e.stopPropagation();

                    const title = form.getAttribute('data-confirm-title') || 'Konfirmasi Tindakan';
                    const message = form.getAttribute('data-confirm-message') || 'Apakah Anda yakin ingin melanjutkan?';

                    Swal.fire({
                        title: title,
                        html: `<p class="text-xs text-neutral-body leading-relaxed text-center mt-1">${message}</p>`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#2E7D32',
                        cancelButtonColor: '#666666',
                        confirmButtonText: 'Ya, Lanjutkan',
                        cancelButtonText: 'Batal',
                        background: '#FFFFFF',
                        reverseButtons: true,
                        customClass: {
                            popup: 'rounded-lg border border-[#E5E5E5] shadow-2xl font-sans text-neutral-dark p-6',
                            title: 'font-sans font-bold text-lg text-neutral-dark',
                            confirmButton: 'rounded-md px-5 py-2.5 font-sans font-semibold text-xs uppercase tracking-wider shadow-sm cursor-pointer ml-2',
                            cancelButton: 'rounded-md px-5 py-2.5 font-sans font-semibold text-xs uppercase tracking-wider shadow-sm cursor-pointer bg-white text-neutral-dark border border-[#E5E5E5] hover:bg-[#F8F8F7]'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.dataset.confirmed = 'true';
                            form.submit();
                        }
                    });
                    return;
                }

                const action = form.getAttribute('action') || '';
                const isLoanStore = (form.hasAttribute('data-confirm-loan')) || 
                                    (action.endsWith('loans') && form.method.toUpperCase() === 'POST' && !action.includes('return') && !action.includes('pay-fine'));

                if (isLoanStore) {
                    e.preventDefault();
                    e.stopPropagation();

                    const bookTitle = form.getAttribute('data-book-title') || 'buku ini';
                    const loanType = form.querySelector('input[name="loan_type"]')?.value || form.getAttribute('data-loan-type') || 'physical';
                    const dueDateInput = form.querySelector('input[name="due_date"]');
                    const dueDate = dueDateInput ? dueDateInput.value : '';

                    let desc = '';
                    if (loanType === 'digital') {
                        desc = `Apakah Anda benar ingin meminjam akses naskah digital untuk buku <strong>"${bookTitle}"</strong> atau ingin mencari koleksi yang lain?`;
                    } else if (dueDate) {
                        desc = `Apakah Anda benar ingin meminjam buku fisik <strong>"${bookTitle}"</strong> dengan rencana pengembalian tanggal <strong>${dueDate}</strong> atau ingin mencari yang lain?`;
                    } else {
                        desc = `Apakah Anda benar ingin mengajukan peminjaman untuk <strong>"${bookTitle}"</strong> atau ingin mencari yang lain?`;
                    }

                    Swal.fire({
                        title: 'Konfirmasi Peminjaman Buku',
                        html: `<p class="text-xs text-neutral-body leading-relaxed text-center mt-1">${desc}</p>`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#C62828',
                        cancelButtonColor: '#666666',
                        confirmButtonText: 'Ya, Pinjam Buku Ini',
                        cancelButtonText: 'Cari yang Lain',
                        background: '#FFFFFF',
                        reverseButtons: true,
                        customClass: {
                            popup: 'rounded-lg border border-[#E5E5E5] shadow-2xl font-sans text-neutral-dark p-6',
                            title: 'font-sans font-bold text-lg text-neutral-dark',
                            confirmButton: 'rounded-md px-5 py-2.5 font-sans font-semibold text-xs uppercase tracking-wider shadow-sm cursor-pointer ml-2',
                            cancelButton: 'rounded-md px-5 py-2.5 font-sans font-semibold text-xs uppercase tracking-wider shadow-sm cursor-pointer bg-white text-neutral-dark border border-[#E5E5E5] hover:bg-[#F8F8F7]'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.dataset.confirmed = 'true';
                            form.submit();
                        }
                    });
                }
            }, true);

            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Operasi Berhasil',
                    text: "{{ session('success') }}",
                    confirmButtonColor: '#C62828',
                    background: '#FFFFFF',
                    customClass: {
                        popup: 'rounded-lg border border-[#E5E5E5] shadow-2xl font-sans text-neutral-dark',
                        confirmButton: 'rounded-md px-6 py-2.5 font-sans font-semibold text-xs uppercase tracking-wider shadow-sm cursor-pointer'
                    }
                });
            @endif
            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kendala',
                    text: "{{ session('error') }}",
                    confirmButtonColor: '#C62828',
                    background: '#FFFFFF',
                    customClass: {
                        popup: 'rounded-lg border border-[#E5E5E5] shadow-2xl font-sans text-neutral-dark',
                        confirmButton: 'rounded-md px-6 py-2.5 font-sans font-semibold text-xs uppercase tracking-wider shadow-sm cursor-pointer'
                    }
                });
            @endif
        });

        // Prevent browser back-forward cache from showing authenticated pages after logout
        window.addEventListener('pageshow', function (event) {
            if (event.persisted || (window.performance && window.performance.navigation && window.performance.navigation.type === 2)) {
                window.location.reload();
            }
        });
    </script>
    @stack('scripts')
</body>

</html>