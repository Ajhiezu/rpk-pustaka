<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>RPK PUSTAKA IMM SAINTEKMU, Modern Academic Editorial Library</title>
        
        <!-- Favicon -->
        <link rel="icon" type="image/x-icon" href="{{ asset('images/logo-rpk.ico') }}">
        <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/logo-rpk.ico') }}">

        <!-- Fonts: Inter Sans-Serif System -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            [x-cloak] { display: none !important; }
            body { font-family: 'Inter', system-ui, -apple-system, sans-serif; }
            .font-serif { font-family: 'Inter', system-ui, -apple-system, sans-serif; }
        </style>
    </head>
    <body class="antialiased bg-white text-neutral-dark selection:bg-primary/10 selection:text-primary"
          x-data="{
              searchQuery: '',
              selectedCategory: 'all',
              selectedType: 'all'
          }">
        @php
            $physicalLoanDays = (int) \App\Models\Setting::get('physical_loan_duration_days', 14);
        @endphp
        
        <!-- Institutional Top Bar -->
        <div class="bg-[#181818] text-[#E5E5E5] text-xs py-2.5 px-6 border-b border-[#262626]">
            <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2 font-medium">
                <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-3 items-center">
                    <span class="text-white font-semibold whitespace-nowrap">RPK PUSTAKA IMM SAINTEKMU</span>
                    <span class="hidden sm:inline text-accent">•</span>
                    <span class="hidden sm:inline text-[#A3A3A3]">Layanan Ruang Baca: Sen – Jum 06:00 – 15:00 WIB</span>
                </div>
                <div class="hidden sm:flex items-center space-x-6 text-[11px] text-[#A3A3A3]">
                    <a href="#koleksi" class="hover:text-white transition-colors">Akses Katalog</a>
                    <a href="#publikasi" class="hover:text-white transition-colors">Artikel & Esai</a>
                    <a href="#layanan" class="hover:text-white transition-colors">Layanan Sirkulasi</a>
                    <a href="#tentang" class="hover:text-white transition-colors">Tentang Kami</a>
                </div>
            </div>
        </div>

        <!-- Red Header / Navigation Bar with Scroll Blur & Glass Shadow Effect (RPK Red #C62828) -->
        <nav x-data="{ scrolled: false }"
             @scroll.window="scrolled = ((window.pageYOffset || document.documentElement.scrollTop) > 20)"
             :class="scrolled ? 'bg-[#C62828]/90 backdrop-blur-md shadow-[0_12px_32px_rgba(0,0,0,0.35)] border-b border-white/20' : 'bg-[#C62828] shadow-md border-b border-white/10'"
             class="sticky top-0 z-50 h-16 sm:h-20 flex items-center transition-all duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 w-full flex justify-between items-center">
                <!-- Academic Logo & Brand -->
                <a href="{{ url('/') }}" class="flex items-center space-x-2 sm:space-x-3.5 group min-w-0">
                    <div class="bg-white p-1.5 sm:p-2 rounded-lg sm:rounded-xl shadow-md flex items-center justify-center shrink-0 transition-transform group-hover:scale-105">
                        <img src="{{ asset('images/logo-rpk.png') }}" alt="RPK PUSTAKA IMM SAINTEKMU" class="h-7 sm:h-9 md:h-10 w-auto object-contain">
                    </div>
                    <div class="min-w-0">
                        <span class="text-lg sm:text-xl md:text-2xl font-bold tracking-tight text-white block leading-none truncate">RPK PUSTAKA</span>
                        <span class="text-[9px] sm:text-[10px] font-semibold text-accent uppercase tracking-wider block mt-0.5 sm:mt-1 whitespace-nowrap">IMM SAINTEKMU</span>
                    </div>
                </a>
                
                <!-- Center Navigation -->
                <div class="hidden lg:flex items-center space-x-8 text-[13px] sm:text-sm font-semibold text-white/90">
                    <a href="#koleksi" class="hover:text-accent transition-colors py-1">Koleksi Pilihan</a>
                    <a href="#publikasi" class="hover:text-accent transition-colors py-1">Artikel & Esai</a>
                    <a href="#layanan" class="hover:text-accent transition-colors py-1">Panduan Meminjam</a>
                    <a href="#tentang" class="hover:text-accent transition-colors py-1">Arsip & Visi</a>
                </div>

                <!-- Right Actions -->
                <div class="flex items-center space-x-2 sm:space-x-3 shrink-0">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="bg-white text-primary hover:bg-neutral-surface text-[11px] sm:text-xs md:text-sm py-1.5 sm:py-2 px-2.5 sm:px-4 shadow-sm font-bold rounded-md transition-all flex items-center">
                            <svg class="w-3.5 sm:w-4 h-3.5 sm:h-4 mr-1 sm:mr-1.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>
                            <span class="whitespace-nowrap">{{ Auth::user()->isAdmin() ? 'Panel Admin' : 'Panel Anggota' }}</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-xs sm:text-sm font-semibold text-white hover:text-accent px-2 sm:px-3 py-1.5 sm:py-2 transition-colors whitespace-nowrap">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" class="bg-white text-primary hover:bg-neutral-surface text-[11px] sm:text-xs md:text-sm py-1.5 sm:py-2 md:py-2.5 px-2.5 sm:px-4 shadow-sm font-bold rounded-md transition-all whitespace-nowrap">
                            Daftar
                        </a>
                    @endauth
                </div>
            </div>
        </nav>

        <!-- Section Pertama: Hero Section (Kembali Putih Seperti Semula) -->
        <header class="pt-10 sm:pt-14 md:pt-16 pb-12 sm:pb-16 md:pb-20 relative overflow-hidden border-b border-neutral-border bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6">
                <!-- Academic Masthead Tag with Small Gold Star Detail -->
                <div class="text-center max-w-3xl mx-auto space-y-3 sm:space-y-4">
                    <div class="inline-flex items-center space-x-1.5 sm:space-x-2 px-3 sm:px-4 py-1 sm:py-1.5 bg-[#F8F8F7] border border-neutral-border rounded-full text-[11px] sm:text-xs font-semibold tracking-wide uppercase text-neutral-dark shadow-xs">
                        <!-- Small Gold Star Accent from Logo -->
                        <svg class="w-3 sm:w-3.5 h-3 sm:h-3.5 text-accent fill-current" viewBox="0 0 24 24">
                            <path d="M12 2l2.4 7.2h7.6l-6.2 4.5 2.4 7.3-6.2-4.6-6.2 4.6 2.4-7.3-6.2-4.5h7.6z"/>
                        </svg>
                        <span>Pusat Preservasi & Akses Pengetahuan</span>
                    </div>

                    <h1 class="text-2xl sm:text-4xl md:text-5xl lg:text-6xl font-bold sm:font-extrabold text-neutral-dark tracking-tight leading-[1.15] sm:leading-[1.18]">
                        Temukan Pengetahuan.<br class="sm:hidden"> <span class="text-primary">Temukan Referensi Ilmiah.</span>
                    </h1>

                    <p class="text-sm sm:text-base md:text-lg text-neutral-body font-normal leading-relaxed max-w-2xl mx-auto pt-1 sm:pt-2">
                        Jelajahi ribuan naskah, literatur akademik, dan karya pemikiran manusia di RPK PUSTAKA IMM SAINTEKMU. Tersedia untuk dibaca, diteliti, dan dipinjam.
                    </p>
                </div>

                <!-- VERY PROMINENT SEARCH CONSOLE -->
                <div class="mt-8 sm:mt-10 md:mt-12 max-w-3xl mx-auto">
                    <div class="bg-white p-2 sm:p-2.5 md:p-3 rounded-lg border-2 border-neutral-border shadow-sm transition-all focus-within:border-primary focus-within:ring-4 focus-within:ring-primary/10">
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                            <div class="flex items-center flex-1 px-2.5 sm:px-3 py-0.5">
                                <svg class="w-4 sm:w-5 h-4 sm:h-5 text-primary mr-2 sm:mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <input 
                                    type="text" 
                                    x-model="searchQuery"
                                    placeholder="Cari judul, penulis, subjek, atau ISBN..." 
                                    class="w-full py-2 sm:py-2.5 bg-transparent border-0 text-xs sm:text-sm md:text-base text-neutral-dark placeholder:text-neutral-muted focus:outline-none focus:ring-0 leading-normal"
                                >
                                <button x-show="searchQuery" @click="searchQuery = ''" class="text-xs text-neutral-muted hover:text-primary px-2" title="Bersihkan">
                                    &times;
                                </button>
                            </div>
                            <a href="#koleksi" 
                               class="btn-editorial py-2.5 sm:py-3 px-4 sm:px-6 text-[11px] sm:text-xs md:text-sm uppercase tracking-wider font-semibold shrink-0 justify-center">
                                Cari Koleksi
                            </a>
                        </div>
                    </div>

                    <!-- Search Shortcuts & Fast Meta -->
                    @if((!empty($popularSearches) && $popularSearches->count() > 0) || $books->count() > 0)
                        <div class="mt-3 sm:mt-4 flex flex-wrap items-center justify-between gap-2 sm:gap-3 text-[11px] sm:text-xs text-neutral-muted px-1 sm:px-2">
                            @if(!empty($popularSearches) && $popularSearches->count() > 0)
                                <div class="flex items-center flex-wrap gap-1 sm:gap-1.5">
                                    <span class="font-semibold text-neutral-dark">Populer:</span>
                                    @foreach($popularSearches as $popular)
                                        <button type="button" @click="searchQuery = '{{ addslashes($popular) }}'" class="hover:text-primary underline decoration-neutral-border underline-offset-2 transition-colors cursor-pointer touch-manipulation">{{ $popular }}</button>
                                        @if(!$loop->last)
                                            <span>•</span>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                            @if(($totalBooksCount ?? $books->count()) > 0)
                                <div class="font-medium text-[11px] sm:text-xs text-neutral-muted {{ (empty($popularSearches) || $popularSearches->count() === 0) ? 'w-full sm:text-right' : '' }}">
                                    Total: {{ $totalBooksCount ?? $allCategories->sum('books_count') }} Koleksi
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Academic Pillar Metrics Strip (RPK Red #C62828 Background) -->
                <div class="mt-10 sm:mt-12 md:mt-16 grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4 md:gap-6 p-4 sm:p-5 md:p-6 lg:p-7 bg-[#C62828] text-white shadow-xl rounded-xl max-w-5xl mx-auto">
                    <div class="text-center sm:text-left px-1">
                        <span class="text-xl sm:text-2xl md:text-3xl font-bold text-white block leading-none">10,000+</span>
                        <span class="text-[10px] sm:text-xs text-white/80 font-medium uppercase tracking-wider block mt-1.5 sm:mt-2">Katalog Terindeks</span>
                    </div>
                    <div class="text-center sm:text-left px-1">
                        <span class="text-xl sm:text-2xl md:text-3xl font-bold text-white block leading-none">100%</span>
                        <span class="text-[10px] sm:text-xs text-white/80 font-medium uppercase tracking-wider block mt-1.5 sm:mt-2">Akses Terbuka</span>
                    </div>
                    <div class="text-center sm:text-left px-1">
                        <span class="text-xl sm:text-2xl md:text-3xl font-bold text-white block leading-none">{{ $physicalLoanDays }} Hari</span>
                        <span class="text-[10px] sm:text-xs text-white/80 font-medium uppercase tracking-wider block mt-1.5 sm:mt-2">Masa Pinjam</span>
                    </div>
                    <div class="text-center sm:text-left px-1">
                        <span class="text-xl sm:text-2xl md:text-3xl font-bold text-accent block leading-none">Aktif</span>
                        <span class="text-[10px] sm:text-xs text-white/80 font-medium uppercase tracking-wider block mt-1.5 sm:mt-2">Layanan</span>
                    </div>
                </div>
            </div>
        </header>

        <!-- Section Katalog Buku (Kembali Putih Seperti Semula) -->
        <section id="koleksi" class="py-12 sm:py-16 md:py-20 bg-white border-b border-neutral-border">
            <div class="max-w-7xl mx-auto px-4 sm:px-6">
                
                <!-- Section Header -->
                <div class="space-y-4 mb-6 sm:mb-8 md:mb-10 pb-4 sm:pb-6 border-b border-neutral-border">
                    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                        <div>
                            <span class="text-[11px] sm:text-xs font-semibold uppercase tracking-wider text-primary block mb-1 sm:mb-1.5">Pilihan Kurator</span>
                            <h2 class="text-xl sm:text-2xl md:text-3xl lg:text-4xl font-bold text-neutral-dark tracking-tight">Koleksi Buku Unggulan</h2>
                            <p class="text-xs sm:text-sm md:text-base text-neutral-body mt-1 sm:mt-1.5 max-w-xl">Koleksi rujukan terpenting yang siap dipelajari di ruang baca maupun dipinjam.</p>
                        </div>

                        <!-- Fiksi / Non-Fiksi Segmented Tabs Filter -->
                        <div class="flex items-center gap-1 p-1 bg-[#F8F8F7] border border-neutral-border rounded-xl self-start md:self-auto shrink-0 shadow-2xs">
                            <button 
                                type="button" 
                                @click="selectedType = 'all'" 
                                :class="selectedType === 'all' ? 'bg-primary text-white font-bold shadow-xs' : 'text-neutral-body hover:text-neutral-dark font-medium'"
                                class="px-3 sm:px-4 py-1.5 rounded-lg text-xs tracking-wide transition-all cursor-pointer">
                                Semua Jenis
                            </button>
                            <button 
                                type="button" 
                                @click="selectedType = 'nonfiksi'" 
                                :class="selectedType === 'nonfiksi' ? 'bg-neutral-800 text-white font-bold shadow-xs' : 'text-neutral-body hover:text-neutral-dark font-medium'"
                                class="px-3 sm:px-4 py-1.5 rounded-lg text-xs tracking-wide transition-all cursor-pointer">
                                Non-Fiksi
                            </button>
                            <button 
                                type="button" 
                                @click="selectedType = 'fiksi'" 
                                :class="selectedType === 'fiksi' ? 'bg-amber-600 text-white font-bold shadow-xs' : 'text-neutral-body hover:text-amber-700 font-medium'"
                                class="px-3 sm:px-4 py-1.5 rounded-lg text-xs tracking-wide transition-all cursor-pointer">
                                Fiksi (Novel)
                            </button>
                        </div>
                    </div>

                    @php
                        $allCategories = $categories ?? \App\Models\Category::whereHas('books')->withCount('books')->orderByDesc('books_count')->get();
                    @endphp
                    @if($allCategories->isNotEmpty())
                        <!-- Category Chips Bar (Smooth Horizontal Scroll on Mobile, Clean Flow on Desktop) -->
                        <div class="w-full overflow-x-auto no-scrollbar -mx-4 px-4 sm:mx-0 sm:px-0 pt-1 pb-1 touch-pan-x">
                            <div class="flex items-center gap-1.5 sm:gap-2 min-w-max py-0.5">
                                <span class="text-[11px] font-semibold uppercase tracking-wider text-neutral-muted mr-1">Kategori:</span>
                                <button 
                                    type="button"
                                    @click="selectedCategory = 'all'" 
                                    :class="selectedCategory === 'all' 
                                        ? 'bg-neutral-dark text-white border-neutral-dark shadow-xs font-bold' 
                                        : 'bg-[#F8F8F7] text-neutral-body border-neutral-border hover:border-primary/60 hover:text-neutral-dark font-medium'"
                                    class="px-3 sm:px-3.5 py-1 rounded-full text-[11px] sm:text-xs uppercase tracking-wide border transition-all duration-200 cursor-pointer whitespace-nowrap flex items-center gap-1 touch-manipulation select-none active:scale-95">
                                    <span>Semua</span>
                                    <span class="text-[10px] opacity-75 font-mono">({{ $totalBooksCount ?? $allCategories->sum('books_count') }})</span>
                                </button>
                                @foreach($allCategories as $cat)
                                    @php
                                        $catSlug = $cat->slug ?: Str::slug($cat->name);
                                    @endphp
                                    <button 
                                        type="button"
                                        @click="selectedCategory = '{{ $catSlug }}'" 
                                        :class="selectedCategory === '{{ $catSlug }}' 
                                            ? 'bg-neutral-dark text-white border-neutral-dark shadow-xs font-bold' 
                                            : 'bg-[#F8F8F7] text-neutral-body border-neutral-border hover:border-primary/60 hover:text-neutral-dark font-medium'"
                                        class="px-3 sm:px-3.5 py-1 rounded-full text-[11px] sm:text-xs uppercase tracking-wide border transition-all duration-200 cursor-pointer whitespace-nowrap flex items-center gap-1 touch-manipulation select-none active:scale-95">
                                        <span>{{ $cat->name }}</span>
                                        @if(!empty($cat->books_count))
                                            <span class="text-[10px] opacity-75 font-mono">({{ $cat->books_count }})</span>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Filtered Grid View of Book Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 sm:gap-6 lg:gap-8">
                    @forelse($books as $book)
                        @php
                            $bookCatSlug = $book->category ? ($book->category->slug ?: Str::slug($book->category->name)) : 'umum';
                            $bookTypeVal = $book->book_type ?? 'fiksi';
                        @endphp
                        <article 
                            data-category="{{ $bookCatSlug }}"
                            data-type="{{ $bookTypeVal }}"
                            data-search="{{ strtolower($book->title . ' ' . $book->author . ' ' . ($book->category->name ?? '')) }}"
                            x-show="(selectedCategory === 'all' || selectedCategory === '{{ $bookCatSlug }}') && (selectedType === 'all' || selectedType === '{{ $bookTypeVal }}') && (!searchQuery || $el.dataset.search.includes(searchQuery.toLowerCase().trim()))"
                            class="group flex flex-col bg-white border border-neutral-border rounded-lg overflow-hidden transition-all duration-300 hover:border-primary/50 hover:shadow-md">
                            
                            <!-- Book Cover as Focal Point -->
                            <div class="relative w-full aspect-[3/4.2] bg-[#F8F8F7] overflow-hidden border-b border-neutral-border">
                                @if($book->cover_url)
                                    <img src="{{ $book->cover_url }}" alt="{{ $book->title }}" 
                                         class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-103">
                                @else
                                    <div class="w-full h-full flex flex-col items-center justify-center p-6 text-center bg-[#F8F8F7]">
                                        <div class="w-12 h-12 rounded-full bg-white flex items-center justify-center text-primary mb-3 shadow-xs border border-neutral-border">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                            </svg>
                                        </div>
                                        <span class="text-sm sm:text-[15px] font-semibold text-neutral-dark line-clamp-2 leading-snug">{{ $book->title }}</span>
                                        <span class="text-xs text-neutral-muted mt-1.5">{{ $book->author }}</span>
                                    </div>
                                @endif

                                <!-- Category & Literary Type Badges on Cover -->
                                <div class="absolute top-3 left-3 flex flex-col gap-1 items-start">
                                    <span class="bg-white/95 backdrop-blur-xs px-2.5 py-1 text-[11px] font-bold text-primary uppercase tracking-wide rounded border border-neutral-border shadow-xs">
                                        {{ $book->category->name ?? 'Umum' }}
                                    </span>
                                    @if($book->isFiction())
                                        <span class="bg-amber-600 text-white px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider rounded shadow-xs">
                                            Fiksi (Novel)
                                        </span>
                                    @endif
                                </div>

                                <!-- Format Badge (FISIK / DIGITAL / FISIK + DIGITAL) -->
                                <div class="absolute top-3 right-3 flex items-center gap-1.5">
                                    @if($book->collection_type === 'fisik_digital')
                                        <span class="bg-primary text-white px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide rounded shadow-xs">
                                            FISIK + DIGITAL
                                        </span>
                                    @elseif($book->collection_type === 'digital')
                                        <span class="bg-[#FFF9ED] text-[#B45309] border border-[#FDE68A] px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide rounded shadow-xs">
                                            DIGITAL
                                        </span>
                                    @else
                                        <span class="bg-[#EDF7ED] text-success border border-[#C8E6C9] px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide rounded shadow-xs">
                                            FISIK
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Book Metadata -->
                            <div class="p-4 sm:p-5 flex-1 flex flex-col justify-between space-y-3 sm:space-y-4">
                                <div>
                                    <div class="flex items-center justify-between gap-2 mb-1 sm:mb-1.5">
                                        <p class="text-[11px] sm:text-xs sm:text-[13px] font-medium text-neutral-muted truncate">{{ $book->author }}</p>
                                        <span class="font-mono text-[10px] text-neutral-muted font-semibold shrink-0">{{ $book->book_code ?? '' }}</span>
                                    </div>
                                    <h3 class="text-sm sm:text-[15px] md:text-base font-semibold text-neutral-dark leading-snug group-hover:text-primary transition-colors line-clamp-2">
                                        <a href="{{ route('public.books.show', $book) }}">
                                            {{ $book->title }}
                                        </a>
                                    </h3>
                                    <p class="text-xs sm:text-sm text-neutral-body mt-1.5 sm:mt-2 line-clamp-2 leading-relaxed">
                                        {{ $book->description ?? 'Buku teks rujukan akademik untuk pembelajaran & riset.' }}
                                    </p>
                                </div>

                                <!-- Bottom Action & Shelf Location -->
                                <div class="pt-2.5 sm:pt-3 border-t border-neutral-border flex items-center justify-between text-[11px] sm:text-xs gap-2">
                                    <div class="flex items-center space-x-1 sm:space-x-1.5 text-neutral-muted min-w-0 flex-1">
                                        @if($book->collection_type === 'digital')
                                            <svg class="w-3 sm:w-3.5 h-3 sm:h-3.5 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                            <span class="text-[10px] sm:text-[11px] sm:text-xs font-semibold uppercase tracking-wide text-primary truncate">E-Book</span>
                                        @else
                                            <svg class="w-3 sm:w-3.5 h-3 sm:h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                                            <span class="text-[10px] sm:text-[11px] sm:text-xs font-semibold uppercase tracking-wide truncate">{{ $book->location->name ?? 'Rak' }}</span>
                                        @endif
                                    </div>

                                    <a href="{{ route('public.books.show', $book) }}" 
                                       class="inline-flex items-center font-semibold text-[11px] sm:text-xs sm:text-sm text-primary hover:text-primary-hover group-hover:underline whitespace-nowrap shrink-0">
                                        Detail
                                        <svg class="w-3 sm:w-3.5 h-3 sm:h-3.5 ml-0.5 sm:ml-1 transition-transform group-hover:translate-x-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="col-span-full py-10 sm:py-14 md:py-20 px-4 sm:px-6 text-center bg-[#F8F8F7] rounded-lg border border-dashed border-neutral-border">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-white flex items-center justify-center mx-auto mb-2 sm:mb-3 text-neutral-muted border border-neutral-border">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                            </div>
                            <p class="text-sm sm:text-base md:text-lg font-bold text-neutral-dark">Koleksi Belum Terdaftar</p>
                            <p class="text-[11px] sm:text-xs md:text-sm text-neutral-muted mt-1 max-w-md mx-auto leading-relaxed">Koleksi buku akan segera diperbarui oleh pustakawan RPK PUSTAKA IMM SAINTEKMU.</p>
                        </div>
                    @endforelse
                </div>

                <!-- Bottom Catalog CTA -->
                @auth
                    @if(Auth::user()->isAdmin())
                        <div class="mt-8 sm:mt-10 md:mt-14 text-center">
                            <a href="{{ route('admin.books.index') }}" class="btn-editorial-outline inline-flex items-center justify-center px-4 sm:px-6 md:px-8 py-2.5 sm:py-3 md:py-3.5 text-[11px] sm:text-xs md:text-sm uppercase tracking-wider font-semibold whitespace-normal text-center leading-snug">
                                Kelola Data Buku &rarr;
                            </a>
                        </div>
                    @else
                        <div class="mt-8 sm:mt-10 md:mt-14 text-center">
                            <a href="{{ route('member.books.index') }}" class="btn-editorial-outline inline-flex items-center justify-center px-4 sm:px-6 md:px-8 py-2.5 sm:py-3 md:py-3.5 text-[11px] sm:text-xs md:text-sm uppercase tracking-wider font-semibold whitespace-normal text-center leading-snug max-w-2xl mx-auto">
                                Lihat Seluruh Katalog ({{ $totalBooksCount ?? $allCategories->sum('books_count') }}+ Buku) &rarr;
                            </a>
                        </div>
                    @endif
                @endauth
            </div>
        </section>

        <!-- Published Articles & Member Essays Section (Original Soft Neutral Styling) -->
        <section id="publikasi" class="py-12 sm:py-16 md:py-20 bg-[#F8F8F7] border-b border-neutral-border">
            <div class="max-w-7xl mx-auto px-4 sm:px-6">
                <!-- Section Header -->
                <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 sm:mb-10 md:mb-12 gap-4 sm:gap-6 border-b border-neutral-border pb-4 sm:pb-6">
                    <div>
                        <span class="text-[11px] sm:text-xs font-semibold uppercase tracking-wider text-primary block mb-1 sm:mb-2">Publikasi & Pemikiran Akademik</span>
                        <h2 class="text-xl sm:text-2xl md:text-3xl lg:text-4xl font-bold text-neutral-dark tracking-tight">Artikel & Esai Terbit</h2>
                        <p class="text-xs sm:text-sm md:text-base text-neutral-body mt-1 sm:mt-2 leading-relaxed max-w-xl">Kumpulan naskah ilmiah, ulasan literatur, dan esai pemikiran yang dipublikasikan oleh pustakawan & anggota RPK PUSTAKA.</p>
                    </div>
                    <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                        <a href="{{ route('public.articles.index') }}" class="btn-editorial-outline inline-flex items-center justify-center text-[11px] sm:text-xs py-2 sm:py-2.5 px-3 sm:px-4 font-semibold uppercase tracking-wider whitespace-normal text-center leading-snug">
                            Lihat Semua Artikel &rarr;
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8 md:gap-10">
                    <!-- Artikel Terbit Strip -->
                    <div class="space-y-4 sm:space-y-6">
                        <div class="flex items-center justify-between pb-2 sm:pb-3 border-b border-neutral-border gap-2">
                            <div class="flex items-center space-x-2">
                                <span class="w-2 sm:w-2.5 h-2 sm:h-2.5 rounded-full bg-primary inline-block shrink-0"></span>
                                <h3 class="text-base sm:text-lg font-bold text-neutral-dark">Artikel Terkini</h3>
                            </div>
                            <span class="text-[10px] sm:text-xs text-neutral-muted whitespace-nowrap">Tinjau Ilmiah</span>
                        </div>

                        @forelse($latestArticles as $article)
                            <article class="p-4 sm:p-5 bg-white rounded-lg border border-neutral-border hover:border-primary/40 hover:shadow-md transition-all duration-200 group flex flex-col sm:flex-row gap-4 sm:gap-5">
                                @if($article->cover_image)
                                    <div class="sm:w-32 sm:h-32 h-40 sm:h-32 w-full sm:w-32 rounded overflow-hidden bg-neutral-surface shrink-0 border border-neutral-border">
                                        <img src="{{ Storage::url($article->cover_image) }}" alt="{{ $article->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    </div>
                                @else
                                    <div class="sm:w-32 sm:h-32 h-28 sm:h-32 w-full sm:w-32 rounded bg-primary-light border border-red-100 flex items-center justify-center shrink-0 text-primary">
                                        <svg class="w-8 sm:w-10 h-8 sm:h-10 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                                    </div>
                                @endif
                                <div class="flex-1 flex flex-col justify-between min-w-0">
                                    <div>
                                        <div class="flex items-center gap-2 text-[10px] sm:text-xs text-neutral-muted mb-1 sm:mb-1.5 flex-wrap">
                                            <span class="font-semibold text-neutral-dark whitespace-nowrap">{{ $article->user->name ?? 'Pustakawan' }}</span>
                                            <span class="shrink-0">•</span>
                                            <span class="whitespace-nowrap">{{ $article->published_at ? $article->published_at->format('d M Y') : $article->created_at->format('d M Y') }}</span>
                                        </div>
                                        <h4 class="text-sm sm:text-base font-bold text-neutral-dark group-hover:text-primary transition-colors line-clamp-2 leading-snug">
                                            <a href="{{ route('public.articles.show', $article->slug) }}">
                                                {{ $article->title }}
                                            </a>
                                        </h4>
                                        <p class="text-[11px] sm:text-xs text-neutral-body mt-1.5 sm:mt-2 line-clamp-2 leading-relaxed">
                                            {{ $article->excerpt ?? Str::limit(strip_tags($article->content), 100) }}
                                        </p>
                                    </div>
                                    <div class="mt-3 sm:mt-4 pt-2.5 sm:pt-3 border-t border-neutral-border flex justify-end">
                                        <a href="{{ route('public.articles.show', $article->slug) }}" class="text-[11px] sm:text-xs font-semibold text-primary hover:underline flex items-center whitespace-nowrap">
                                            Baca Selengkapnya
                                            <svg class="w-3 sm:w-3.5 h-3 sm:h-3.5 ml-0.5 sm:ml-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                        </a>
                                    </div>
                                </div>
                            </article>
                        @empty
                            <div class="p-5 sm:p-8 bg-white rounded-lg border border-dashed border-neutral-border text-center space-y-2.5 sm:space-y-3">
                                <div class="w-9 sm:w-10 h-9 sm:h-10 rounded-full bg-primary-light text-primary flex items-center justify-center mx-auto">
                                    <svg class="w-4.5 sm:w-5 h-4.5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                                </div>
                                <h4 class="text-sm font-bold text-neutral-dark">Belum Ada Artikel</h4>
                                <p class="text-[11px] sm:text-xs text-neutral-muted max-w-sm mx-auto leading-relaxed">Artikel ilmiah dan hasil riset akan ditampilkan di sini setelah disetujui kurator.</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Esai & Karya Anggota Strip -->
                    <div class="space-y-4 sm:space-y-6">
                        <div class="flex items-center justify-between pb-2 sm:pb-3 border-b border-neutral-border gap-2">
                            <div class="flex items-center space-x-2">
                                <span class="w-2 sm:w-2.5 h-2 sm:h-2.5 rounded-full bg-accent inline-block shrink-0"></span>
                                <h3 class="text-base sm:text-lg font-bold text-neutral-dark">Esai & Karya Anggota</h3>
                            </div>
                            <span class="text-[10px] sm:text-xs text-neutral-muted whitespace-nowrap">Terakreditasi</span>
                        </div>

                        @forelse($publishedEssays as $essay)
                            <article class="p-4 sm:p-5 bg-white rounded-lg border border-neutral-border hover:border-accent transition-all duration-200 group flex flex-col justify-between space-y-3 sm:space-y-4 shadow-xs">
                                <div>
                                    <div class="flex items-center justify-between gap-2 mb-1.5 sm:mb-2 flex-wrap">
                                        <span class="inline-flex items-center px-2 sm:px-2.5 py-0.5 rounded-full text-[10px] font-semibold uppercase tracking-wider bg-accent-light text-neutral-dark border border-[#FDE68A] whitespace-nowrap">
                                            Esai Anggota
                                        </span>
                                        <span class="text-[10px] sm:text-xs text-neutral-muted whitespace-nowrap">
                                            {{ $essay->created_at->format('d M Y') }}
                                        </span>
                                    </div>
                                    <h4 class="text-sm sm:text-base font-bold text-neutral-dark group-hover:text-primary transition-colors line-clamp-2 leading-snug">
                                        @auth
                                            <a href="{{ Auth::user()->isAdmin() ? route('admin.essays.show', $essay) : route('anggota.essays.show', $essay) }}">
                                                {{ $essay->title }}
                                            </a>
                                        @else
                                            <a href="{{ route('login') }}">
                                                {{ $essay->title }}
                                            </a>
                                        @endauth
                                    </h4>
                                    <p class="text-[11px] sm:text-xs text-neutral-body mt-1.5 sm:mt-2 line-clamp-3 leading-relaxed">
                                        {{ Str::limit(strip_tags($essay->content ?? 'Esai berbasis dokumen naskah terlampir.'), 140) }}
                                    </p>
                                </div>
                                <div class="pt-2.5 sm:pt-3 border-t border-neutral-border flex items-center justify-between text-[11px] sm:text-xs gap-2">
                                    <span class="font-medium text-neutral-muted min-w-0 truncate">Oleh: <strong class="text-neutral-dark whitespace-nowrap">{{ $essay->user->name ?? 'Anggota IMM' }}</strong></span>
                                    @auth
                                        <a href="{{ Auth::user()->isAdmin() ? route('admin.essays.show', $essay) : route('anggota.essays.show', $essay) }}" class="font-semibold text-primary hover:underline flex items-center whitespace-nowrap shrink-0">
                                            Baca Esai
                                            <svg class="w-3 sm:w-3.5 h-3 sm:h-3.5 ml-0.5 sm:ml-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                        </a>
                                    @else
                                        <a href="{{ route('login') }}" class="font-semibold text-primary hover:underline flex items-center whitespace-nowrap shrink-0">
                                            Masuk Baca
                                            <svg class="w-3 sm:w-3.5 h-3 sm:h-3.5 ml-0.5 sm:ml-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                        </a>
                                    @endauth
                                </div>
                            </article>
                        @empty
                            <div class="p-5 sm:p-8 bg-white rounded-lg border border-dashed border-neutral-border text-center space-y-2.5 sm:space-y-3">
                                <div class="w-9 sm:w-10 h-9 sm:h-10 rounded-full bg-accent-light text-neutral-dark flex items-center justify-center mx-auto border border-[#FDE68A]">
                                    <svg class="w-4.5 sm:w-5 h-4.5 sm:h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002-2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </div>
                                <h4 class="text-sm font-bold text-neutral-dark">Belum Ada Esai Terbit</h4>
                                <p class="text-[11px] sm:text-xs text-neutral-muted max-w-sm mx-auto leading-relaxed">Kirimkan gagasan dan karya esai Anda sebagai anggota melalui dasbor keanggotaan.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </section>

        <!-- Red Service Standard Section (RPK Red #C62828 Background) -->
        <section id="layanan" class="py-14 sm:py-16 md:py-20 lg:py-24 bg-[#C62828] text-white overflow-hidden relative shadow-inner">
            <!-- Background Ambient Circle Effects -->
            <div class="absolute -right-20 -bottom-20 w-80 h-80 rounded-full bg-white/5 blur-3xl pointer-events-none"></div>
            <div class="absolute -left-20 -top-20 w-80 h-80 rounded-full bg-black/10 blur-3xl pointer-events-none"></div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 relative z-10">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 sm:gap-10 md:gap-12 lg:gap-16 items-center">
                    <div class="lg:col-span-6 space-y-4 sm:space-y-5 md:space-y-6">
                        <span class="inline-flex items-center gap-1 sm:gap-1.5 px-2.5 sm:px-3 py-0.5 sm:py-1 bg-white/10 rounded-full text-white text-[11px] sm:text-xs font-semibold uppercase tracking-wider border border-white/20">
                            <svg class="w-3 sm:w-3.5 h-3 sm:h-3.5 text-accent fill-current shrink-0" viewBox="0 0 24 24">
                                <path d="M12 2l2.4 7.2h7.6l-6.2 4.5 2.4 7.3-6.2-4.6-6.2 4.6 2.4-7.3-6.2-4.5h7.6z"/>
                            </svg>
                            Standar Pelayanan
                        </span>
                        <h2 class="text-xl sm:text-2xl md:text-3xl lg:text-4xl font-bold text-white tracking-tight leading-tight">
                            Ruang Sunyi untuk Konsentrasi & Refleksi
                        </h2>
                        <p class="text-xs sm:text-sm md:text-base text-white/90 leading-relaxed">
                            Kami memadukan kenyamanan ruang baca fisik yang hening dengan ketepatan katalog digital. Anggota terdaftar dapat mereservasi buku langsung secara daring dan mengambilnya di loket sirkulasi.
                        </p>

                        <div class="space-y-3 sm:space-y-4 pt-3 sm:pt-4 border-t border-white/20">
                            <div class="flex items-start space-x-2.5 sm:space-x-3.5">
                                <div class="w-5 sm:w-6 h-5 sm:h-6 rounded-full bg-white/20 text-white flex items-center justify-center shrink-0 mt-0.5 border border-white/30">
                                    <svg class="w-3 sm:w-3.5 h-3 sm:h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                <div class="min-w-0">
                                    <h4 class="text-xs sm:text-sm md:text-base font-semibold text-white">Peminjaman Tanpa Hambatan</h4>
                                    <p class="text-[11px] sm:text-xs md:text-sm text-white/80 mt-0.5 leading-relaxed">Ajukan peminjaman buku dengan tenggat {{ $physicalLoanDays }} hari melalui dasbor anggota.</p>
                                </div>
                            </div>

                            <div class="flex items-start space-x-2.5 sm:space-x-3.5">
                                <div class="w-5 sm:w-6 h-5 sm:h-6 rounded-full bg-white/20 text-white flex items-center justify-center shrink-0 mt-0.5 border border-white/30">
                                    <svg class="w-3 sm:w-3.5 h-3 sm:h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                <div class="min-w-0">
                                    <h4 class="text-xs sm:text-sm md:text-base font-semibold text-white">Pustakawan Kurasi Profesional</h4>
                                    <p class="text-[11px] sm:text-xs md:text-sm text-white/80 mt-0.5 leading-relaxed">Staf pustaka siap mendampingi pencarian sumber referensi.</p>
                                </div>
                            </div>

                            <div class="flex items-start space-x-2.5 sm:space-x-3.5">
                                <div class="w-5 sm:w-6 h-5 sm:h-6 rounded-full bg-white/20 text-white flex items-center justify-center shrink-0 mt-0.5 border border-white/30">
                                    <svg class="w-3 sm:w-3.5 h-3 sm:h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                <div class="min-w-0">
                                    <h4 class="text-xs sm:text-sm md:text-base font-semibold text-white">Riwayat & Notifikasi Tenggat</h4>
                                    <p class="text-[11px] sm:text-xs md:text-sm text-white/80 mt-0.5 leading-relaxed">Pantau status pinjaman, tanggal jatuh tempo, dan riwayat sirkulasi secara transparan.</p>
                                </div>
                            </div>
                        </div>

                        <div class="pt-3 sm:pt-4">
                            <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-4 sm:px-5 md:px-6 py-2.5 sm:py-3 md:py-3.5 bg-white text-primary hover:bg-neutral-surface font-bold text-[11px] sm:text-xs md:text-sm uppercase tracking-wider rounded-lg shadow-xl transition-all hover:scale-[1.01] whitespace-normal text-center leading-snug w-full sm:w-auto max-w-xl">
                                Bergabung Jadi Anggota RPK PUSTAKA
                            </a>
                        </div>
                    </div>

                    <div class="lg:col-span-6">
                        <div class="bg-white text-neutral-dark border-0 p-5 sm:p-6 md:p-8 rounded-2xl shadow-2xl space-y-4 sm:space-y-5 md:space-y-6">
                            <div class="border-b border-neutral-border pb-4 sm:pb-5 md:pb-6">
                                <span class="text-[11px] sm:text-xs font-semibold uppercase tracking-wider text-primary">Pedoman Sirkulasi</span>
                                <h3 class="text-lg sm:text-xl md:text-2xl font-bold text-neutral-dark mt-1">Ketentuan Keanggotaan</h3>
                            </div>

                            <div class="space-y-3 sm:space-y-4 text-[11px] sm:text-xs md:text-sm text-neutral-body leading-relaxed">
                                <div class="flex flex-col sm:flex-row sm:items-baseline justify-between border-b border-neutral-border pb-2.5 sm:pb-3 gap-1">
                                    <span class="font-semibold text-neutral-dark whitespace-nowrap">Batas Pengembalian:</span>
                                    <span class="font-medium text-neutral-dark">{{ $physicalLoanDays }} Hari (Dapat Diperpanjang)</span>
                                </div>
                                <div class="flex flex-col sm:flex-row sm:items-baseline justify-between border-b border-neutral-border pb-2.5 sm:pb-3 gap-1">
                                    <span class="font-semibold text-neutral-dark whitespace-nowrap">Denda Keterlambatan:</span>
                                    <span class="font-medium text-neutral-dark">Otomatis Berdasarkan Aturan</span>
                                </div>
                                <div class="flex flex-col sm:flex-row sm:items-baseline justify-between gap-1">
                                    <span class="font-semibold text-neutral-dark whitespace-nowrap">Kondisi Buku:</span>
                                    <span class="font-medium text-neutral-dark">Kebersihan & Keutuhan Halaman</span>
                                </div>
                            </div>

                            <div class="bg-[#F8F8F7] p-3 sm:p-4 rounded-lg border border-neutral-border text-[11px] sm:text-xs md:text-sm text-neutral-body italic leading-relaxed">
                                "Menjaga buku sama halnya menghormati ribuan pemikiran yang mendahului kita."
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Dignified Institution Footer -->
        <footer id="tentang" class="bg-[#181818] text-[#E5E5E5] pt-10 sm:pt-14 md:pt-16 pb-8 sm:pb-10 md:pb-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-8 sm:gap-10 md:gap-12 pb-10 sm:pb-12 md:pb-14 border-b border-[#262626]">
                    <!-- Brand Column -->
                    <div class="md:col-span-5 space-y-3 sm:space-y-4">
                        <div class="flex items-center space-x-2 sm:space-x-3">
                            <img src="{{ asset('images/logo-rpk.png') }}" alt="RPK PUSTAKA IMM SAINTEKMU" class="h-8 sm:h-9 md:h-10 w-auto object-contain bg-white rounded p-0.5 sm:p-1">
                            <div class="min-w-0">
                                <span class="text-lg sm:text-xl font-bold tracking-tight text-white block leading-none whitespace-nowrap">RPK PUSTAKA</span>
                                <span class="text-[9px] sm:text-[10px] uppercase tracking-wider text-[#A3A3A3] font-semibold block mt-0.5 sm:mt-1 whitespace-nowrap">IMM SAINTEKMU</span>
                            </div>
                        </div>
                        <p class="text-[11px] sm:text-xs md:text-[13px] text-[#A3A3A3] leading-relaxed max-w-sm pt-1 sm:pt-2">
                            Lembaga perpustakaan riset dan dokumentasi ilmiah. Didedikasikan untuk memelihara warisan pemikiran dan menyediakan akses setara terhadap ilmu pengetahuan.
                        </p>
                        <div class="flex items-center gap-1.5 sm:gap-2 pt-1 sm:pt-2">
                            <svg class="w-3 sm:w-3.5 h-3 sm:h-3.5 text-accent fill-current shrink-0" viewBox="0 0 24 24">
                                <path d="M12 2l2.4 7.2h7.6l-6.2 4.5 2.4 7.3-6.2-4.6-6.2 4.6 2.4-7.3-6.2-4.5h7.6z"/>
                            </svg>
                            <p class="italic text-[11px] sm:text-xs md:text-[13px] text-white font-medium">
                                Mencerahkan Generasi Melalui Akses Pengetahuan
                            </p>
                        </div>
                    </div>

                    <!-- Navigation Links -->
                    <div class="md:col-span-3 space-y-2.5 sm:space-y-3">
                        <h4 class="text-[11px] sm:text-xs md:text-[13px] font-semibold uppercase tracking-wider text-white">Navigasi Utama</h4>
                        <ul class="space-y-2 sm:space-y-2.5 text-[11px] sm:text-xs md:text-[13px] text-[#A3A3A3]">
                            <li><a href="#koleksi" class="hover:text-white transition-colors">Katalog Buku Pilihan</a></li>
                            <li><a href="#kategori" class="hover:text-white transition-colors">Kategori Koleksi</a></li>
                            @auth
                                <li><a href="{{ url('/dashboard') }}" class="hover:text-white transition-colors">{{ Auth::user()->isAdmin() ? 'Dasbor Admin' : 'Dasbor Anggota' }}</a></li>
                            @else
                                <li><a href="{{ route('login') }}" class="hover:text-white transition-colors">Masuk ke Portal</a></li>
                                <li><a href="{{ route('register') }}" class="hover:text-white transition-colors">Pendaftaran Anggota</a></li>
                            @endauth
                        </ul>
                    </div>

                    <!-- Operational Hours -->
                    <div class="md:col-span-4 space-y-2.5 sm:space-y-3">
                        <h4 class="text-[11px] sm:text-xs md:text-[13px] font-semibold uppercase tracking-wider text-white">Jam Layanan Sirkulasi</h4>
                        <div class="space-y-1.5 sm:space-y-2 text-[11px] sm:text-xs md:text-[13px] text-[#A3A3A3]">
                            <div class="flex justify-between border-b border-[#262626] pb-1 sm:pb-1.5 gap-2">
                                <span class="whitespace-nowrap">Senin – Jumat:</span>
                                <span class="text-white font-medium whitespace-nowrap">06:00 – 15:00 WIB</span>
                            </div>
                            <div class="flex justify-between pt-0.5 sm:pt-1 gap-2">
                                <span class="whitespace-nowrap">Sabtu – Minggu:</span>
                                <span class="text-[#737373] italic whitespace-nowrap">Tutup (Digital 24 Jam)</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Copyright Bar -->
                <div class="pt-6 sm:pt-8 flex flex-col sm:flex-row items-center sm:items-start justify-between text-[11px] sm:text-xs text-[#888888] gap-3 sm:gap-4">
                    <p class="text-center sm:text-left leading-relaxed">&copy; {{ date('Y') }} RPK PUSTAKA IMM SAINTEKMU, Hak Cipta Dilindungi. Sesuai Standar Tata Kelola Perpustakaan Digital Nasional.</p>
                    <p class="font-medium whitespace-nowrap">IMM SAINTEKMU</p>
                </div>
            </div>
        </footer>

        <script>
            window.initialBooks = @json($books);
        </script>
    </body>
</html>
