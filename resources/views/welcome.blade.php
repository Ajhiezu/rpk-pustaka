<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>RPK PUSTAKA IMM SAINTEKMU — Modern Academic Editorial Library</title>
        
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
    <body class="antialiased bg-white text-neutral-dark selection:bg-primary/10 selection:text-primary">
        
        <!-- Institutional Top Bar -->
        <div class="bg-[#181818] text-[#E5E5E5] text-xs py-2.5 px-6 border-b border-[#262626]">
            <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2 font-medium">
                <div class="flex items-center space-x-3">
                    <span class="text-white font-semibold whitespace-nowrap">RPK PUSTAKA IMM SAINTEKMU</span>
                    <span class="text-accent">•</span>
                    <span class="text-[#A3A3A3]">Layanan Ruang Baca: Sen – Jum 06:00 – 00:00 WIB</span>
                </div>
                <div class="flex items-center space-x-6 text-[11px] text-[#A3A3A3]">
                    <a href="#koleksi" class="hover:text-white transition-colors">Akses Katalog</a>
                    <a href="#publikasi" class="hover:text-white transition-colors">Artikel & Esai</a>
                    <a href="#layanan" class="hover:text-white transition-colors">Layanan Sirkulasi</a>
                    <a href="#tentang" class="hover:text-white transition-colors">Tentang Kami</a>
                </div>
            </div>
        </div>

        <!-- Modern Academic RPK PUSTAKA IMM SAINTEKMU Header / Navigation -->
        <nav class="h-20 bg-white/95 backdrop-blur-md border-b border-neutral-border sticky top-0 z-50 flex items-center shadow-xs">
            <div class="max-w-7xl mx-auto px-6 w-full flex justify-between items-center">
                <!-- Academic Logo & Brand -->
                <a href="{{ url('/') }}" class="flex items-center space-x-3.5 group">
                    <img src="{{ asset('images/logo-rpk.png') }}" alt="RPK PUSTAKA IMM SAINTEKMU" class="h-11 w-auto object-contain">
                    <div>
                        <span class="text-xl sm:text-2xl font-bold tracking-tight text-neutral-dark block leading-none">RPK PUSTAKA</span>
                        <span class="text-[10px] font-semibold text-neutral-muted uppercase tracking-wider block mt-1 whitespace-nowrap">IMM SAINTEKMU</span>
                    </div>
                </a>
                
                <!-- Center Navigation -->
                <div class="hidden lg:flex items-center space-x-8 text-[13px] sm:text-sm font-medium text-neutral-dark">
                    <a href="#koleksi" class="hover:text-primary transition-colors py-1">Koleksi Pilihan</a>
                    <a href="#publikasi" class="hover:text-primary transition-colors py-1">Artikel & Esai</a>
                    <a href="#layanan" class="hover:text-primary transition-colors py-1">Panduan Meminjam</a>
                    <a href="#tentang" class="hover:text-primary transition-colors py-1">Arsip & Visi</a>
                </div>

                <!-- Right Actions -->
                <div class="flex items-center space-x-3">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn-editorial text-xs sm:text-sm py-2 px-4 shadow-xs font-semibold">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>
                            {{ Auth::user()->isAdmin() ? 'Panel Admin' : 'Panel Anggota' }}
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-[13px] sm:text-sm font-medium text-neutral-dark hover:text-primary px-3 py-2 transition-colors">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" class="btn-editorial text-xs sm:text-sm py-2 px-4 shadow-xs font-semibold">
                            Daftar Anggota
                        </a>
                    @endauth
                </div>
            </div>
        </nav>

        <!-- Large Editorial Hero with Dominant White & Prominent Search -->
        <header class="pt-16 pb-20 relative overflow-hidden border-b border-neutral-border bg-white"
                x-data="{
                    searchQuery: '',
                    selectedCategory: 'all',
                    get filteredBooks() {
                        const q = this.searchQuery.toLowerCase().trim();
                        return window.initialBooks.filter(book => {
                            const matchQuery = !q || 
                                book.title.toLowerCase().includes(q) || 
                                book.author.toLowerCase().includes(q) ||
                                (book.category && book.category.name.toLowerCase().includes(q)) ||
                                (book.isbn && book.isbn.includes(q));
                            const matchCat = this.selectedCategory === 'all' || 
                                (book.category && book.category.slug === this.selectedCategory);
                            return matchQuery && matchCat;
                        });
                    }
                }">

            <div class="max-w-7xl mx-auto px-6">
                <!-- Academic Masthead Tag with Small Gold Star Detail -->
                <div class="text-center max-w-3xl mx-auto space-y-4">
                    <div class="inline-flex items-center space-x-2 px-4 py-1.5 bg-[#F8F8F7] border border-neutral-border rounded-full text-xs font-semibold tracking-wide uppercase text-neutral-dark shadow-xs">
                        <!-- Small Gold Star Accent from Logo -->
                        <svg class="w-3.5 h-3.5 text-accent fill-current" viewBox="0 0 24 24">
                            <path d="M12 2l2.4 7.2h7.6l-6.2 4.5 2.4 7.3-6.2-4.6-6.2 4.6 2.4-7.3-6.2-4.5h7.6z"/>
                        </svg>
                        <span>Pusat Preservasi & Akses Pengetahuan Terbuka</span>
                    </div>

                    <h1 class="text-3xl sm:text-5xl md:text-6xl font-bold sm:font-extrabold text-neutral-dark tracking-tight leading-[1.18]">
                        Temukan Pengetahuan. <br><span class="text-primary">Temukan Referensi Ilmiah.</span>
                    </h1>

                    <p class="text-base sm:text-lg text-neutral-body font-normal leading-relaxed max-w-2xl mx-auto pt-2">
                        Jelajahi ribuan naskah, literatur akademik, dan karya pemikiran manusia di RPK PUSTAKA IMM SAINTEKMU. Tersedia untuk dibaca, diteliti, dan dipinjam secara langsung maupun digital.
                    </p>
                </div>

                <!-- VERY PROMINENT SEARCH CONSOLE -->
                <div class="mt-12 max-w-3xl mx-auto">
                    <div class="bg-white p-2.5 sm:p-3 rounded-lg border-2 border-neutral-border shadow-sm transition-all focus-within:border-primary focus-within:ring-4 focus-within:ring-primary/10">
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                            <div class="flex items-center flex-1 px-3">
                                <svg class="w-5 h-5 text-primary mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <input 
                                    type="text" 
                                    x-model="searchQuery"
                                    placeholder="Cari judul koleksi, nama penulis, subjek, atau ISBN..." 
                                    class="w-full py-2.5 bg-transparent border-0 text-sm sm:text-base text-neutral-dark placeholder:text-neutral-muted focus:outline-none focus:ring-0 leading-normal"
                                >
                                <button x-show="searchQuery" @click="searchQuery = ''" class="text-xs text-neutral-muted hover:text-primary px-2" title="Bersihkan">
                                    &times;
                                </button>
                            </div>
                            <a href="#koleksi" 
                               class="btn-editorial py-3 px-6 text-xs sm:text-sm uppercase tracking-wider font-semibold shrink-0 justify-center">
                                Cari Koleksi
                            </a>
                        </div>
                    </div>

                    <!-- Search Shortcuts & Fast Meta -->
                    @if((!empty($popularSearches) && $popularSearches->count() > 0) || $books->count() > 0)
                        <div class="mt-4 flex flex-wrap items-center justify-between gap-3 text-xs text-neutral-muted px-2">
                            @if(!empty($popularSearches) && $popularSearches->count() > 0)
                                <div class="flex items-center flex-wrap gap-1.5">
                                    <span class="font-semibold text-neutral-dark">Pencarian Populer:</span>
                                    @foreach($popularSearches as $popular)
                                        <button @click="searchQuery = '{{ $popular }}'" class="hover:text-primary underline decoration-neutral-border underline-offset-2 transition-colors cursor-pointer">{{ $popular }}</button>
                                        @if(!$loop->last)
                                            <span>•</span>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                            @if($books->count() > 0)
                                <div class="font-medium text-xs text-neutral-muted {{ (empty($popularSearches) || $popularSearches->count() === 0) ? 'w-full text-right' : '' }}">
                                    Total Koleksi: {{ $books->count() }} Terdata
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Academic Pillar Metrics Strip (Soft Neutral Background) -->
                <div class="mt-16 grid grid-cols-2 md:grid-cols-4 gap-6 p-6 sm:p-7 bg-[#F8F8F7] border border-neutral-border rounded-lg max-w-5xl mx-auto">
                    <div class="text-center sm:text-left">
                        <span class="text-2xl sm:text-3xl font-bold text-neutral-dark block leading-none">10,000+</span>
                        <span class="text-xs text-neutral-muted font-medium uppercase tracking-wider block mt-2">Katalog Terindeks</span>
                    </div>
                    <div class="text-center sm:text-left">
                        <span class="text-2xl sm:text-3xl font-bold text-neutral-dark block leading-none">100%</span>
                        <span class="text-xs text-neutral-muted font-medium uppercase tracking-wider block mt-2">Akses Terbuka</span>
                    </div>
                    <div class="text-center sm:text-left">
                        <span class="text-2xl sm:text-3xl font-bold text-neutral-dark block leading-none">14 Hari</span>
                        <span class="text-xs text-neutral-muted font-medium uppercase tracking-wider block mt-2">Masa Pinjam Standar</span>
                    </div>
                    <div class="text-center sm:text-left">
                        <span class="text-2xl sm:text-3xl font-bold text-primary block leading-none">Aktif</span>
                        <span class="text-xs text-neutral-muted font-medium uppercase tracking-wider block mt-2">Layanan Sirkulasi</span>
                    </div>
                </div>
            </div>

            <!-- Curated Masterpieces / Book Catalog Section -->
            <section id="koleksi" class="mt-20 pt-16 bg-white border-t border-neutral-border">
                <div class="max-w-7xl mx-auto px-6">
                    
                    <!-- Section Header -->
                    <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 pb-6 border-b border-neutral-border gap-6">
                        <div>
                            <span class="text-xs font-semibold uppercase tracking-wider text-primary block mb-1.5">Pilihan Kurator</span>
                            <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-neutral-dark tracking-tight">Koleksi Buku Unggulan</h2>
                            <p class="text-sm sm:text-base text-neutral-body mt-1.5 max-w-xl">Koleksi rujukan terpenting yang siap dipelajari di ruang baca maupun dipinjam ke rumah.</p>
                        </div>

                        @php
                            $uniqueCategories = $books->pluck('category')->filter()->unique('id');
                        @endphp
                        @if($uniqueCategories->isNotEmpty())
                            <!-- Category Chips -->
                            <div class="flex items-center flex-wrap gap-2">
                                <button 
                                    @click="selectedCategory = 'all'" 
                                    :class="selectedCategory === 'all' ? 'bg-primary text-white border-primary' : 'bg-white text-neutral-body border-neutral-border hover:border-primary hover:text-primary'"
                                    class="px-4 py-2 rounded-md text-xs sm:text-[13px] font-semibold uppercase tracking-wide border transition-colors cursor-pointer">
                                    Semua Kategori
                                </button>
                                @foreach($uniqueCategories as $cat)
                                    <button 
                                        @click="selectedCategory = '{{ $cat->slug }}'" 
                                        :class="selectedCategory === '{{ $cat->slug }}' ? 'bg-primary text-white border-primary' : 'bg-white text-neutral-body border-neutral-border hover:border-primary hover:text-primary'"
                                        class="px-4 py-2 rounded-md text-xs sm:text-[13px] font-semibold uppercase tracking-wide border transition-colors cursor-pointer">
                                        {{ $cat->name }}
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Filtered Grid View of Book Cards -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                        @forelse($books as $book)
                            <article 
                                x-show="(!searchQuery || '{{ strtolower(addslashes($book->title . ' ' . $book->author . ' ' . ($book->category->name ?? ''))) }}'.includes(searchQuery.toLowerCase().trim())) && (selectedCategory === 'all' || selectedCategory === '{{ $book->category->slug ?? '' }}')"
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

                                    <!-- Category Badge on Cover -->
                                    <div class="absolute top-3 left-3">
                                        <span class="bg-white/95 backdrop-blur-xs px-2.5 py-1 text-[11px] font-bold text-primary uppercase tracking-wide rounded border border-neutral-border shadow-xs">
                                            {{ $book->category->name ?? 'Umum' }}
                                        </span>
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
                                <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
                                    <div>
                                        <div class="flex items-center justify-between gap-2 mb-1.5">
                                            <p class="text-xs sm:text-[13px] font-medium text-neutral-muted truncate">{{ $book->author }}</p>
                                            <span class="font-mono text-[10px] text-neutral-muted font-semibold shrink-0">{{ $book->book_code ?? '' }}</span>
                                        </div>
                                        <h3 class="text-[15px] sm:text-base font-semibold text-neutral-dark leading-snug group-hover:text-primary transition-colors line-clamp-2">
                                            <a href="{{ route('public.books.show', $book) }}">
                                                {{ $book->title }}
                                            </a>
                                        </h3>
                                        <p class="text-xs sm:text-sm text-neutral-body mt-2 line-clamp-2 leading-relaxed">
                                            {{ $book->description ?? 'Buku teks rujukan akademik untuk keperluan pembelajaran dan riset mendalam.' }}
                                        </p>
                                    </div>

                                    <!-- Bottom Action & Shelf Location -->
                                    <div class="pt-3 border-t border-neutral-border flex items-center justify-between text-xs">
                                        <div class="flex items-center space-x-1.5 text-neutral-muted">
                                            @if($book->collection_type === 'digital')
                                                <svg class="w-3.5 h-3.5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                                <span class="text-[11px] sm:text-xs font-semibold uppercase tracking-wide text-primary">E-Book Digital</span>
                                            @else
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                                                <span class="text-[11px] sm:text-xs font-semibold uppercase tracking-wide">{{ $book->location->name ?? 'Rak Utama' }}</span>
                                            @endif
                                        </div>

                                        <a href="{{ route('public.books.show', $book) }}" 
                                           class="inline-flex items-center font-semibold text-xs sm:text-sm text-primary hover:text-primary-hover group-hover:underline">
                                            Buka Detail
                                            <svg class="w-3.5 h-3.5 ml-1 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </article>
                        @empty
                            <div class="col-span-full py-20 text-center bg-[#F8F8F7] rounded-lg border border-dashed border-neutral-border">
                                <div class="w-12 h-12 rounded-full bg-white flex items-center justify-center mx-auto mb-3 text-neutral-muted border border-neutral-border">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                </div>
                                <p class="text-base sm:text-lg font-bold text-neutral-dark">Koleksi Belum Terdaftar</p>
                                <p class="text-xs sm:text-sm text-neutral-muted mt-1">Koleksi buku akan segera diperbarui oleh pustakawan RPK PUSTAKA IMM SAINTEKMU.</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Bottom Catalog CTA -->
                    @auth
                        @if(Auth::user()->isAdmin())
                            <div class="mt-14 text-center">
                                <a href="{{ route('admin.books.index') }}" class="btn-editorial-outline px-8 py-3.5 text-xs sm:text-sm uppercase tracking-wider font-semibold">
                                    Kelola Master Data Buku &rarr;
                                </a>
                            </div>
                        @else
                            <div class="mt-14 text-center">
                                <a href="{{ route('member.books.index') }}" class="btn-editorial-outline px-8 py-3.5 text-xs sm:text-sm uppercase tracking-wider font-semibold">
                                    Lihat Seluruh Katalog Anggota ({{ $books->count() }}+ Buku) &rarr;
                                </a>
                            </div>
                        @endif
                    @endauth
                </div>
            </section>
        </header>

        <!-- Published Articles & Member Essays Section -->
        <section id="publikasi" class="py-20 bg-[#F8F8F7] border-b border-neutral-border">
            <div class="max-w-7xl mx-auto px-6">
                <!-- Section Header -->
                <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-6 border-b border-neutral-border pb-6">
                    <div>
                        <span class="text-xs font-semibold uppercase tracking-wider text-primary block mb-2">Publikasi & Pemikiran Akademik</span>
                        <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-neutral-dark tracking-tight">Artikel & Esai Terbit</h2>
                        <p class="text-sm sm:text-base text-neutral-body mt-2 leading-relaxed">Kumpulan naskah ilmiah, ulasan literatur, dan esai pemikiran yang dipublikasikan oleh pustakawan dan anggota RPK PUSTAKA.</p>
                    </div>
                    <div class="flex items-center gap-3 shrink-0">
                        <a href="{{ route('public.articles.index') }}" class="btn-editorial-outline text-xs py-2.5 px-4 font-semibold uppercase tracking-wider">
                            Lihat Semua Artikel &rarr;
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                    <!-- Artikel Terbit Strip -->
                    <div class="space-y-6">
                        <div class="flex items-center justify-between pb-3 border-b border-neutral-border">
                            <div class="flex items-center space-x-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-primary inline-block"></span>
                                <h3 class="text-lg font-bold text-neutral-dark">Artikel Terkini</h3>
                            </div>
                            <span class="text-xs text-neutral-muted">Terbukti & Tinjau Ilmiah</span>
                        </div>

                        @forelse($latestArticles as $article)
                            <article class="p-5 bg-white rounded-lg border border-neutral-border hover:border-primary/40 hover:shadow-md transition-all duration-200 group flex flex-col sm:flex-row gap-5">
                                @if($article->cover_image)
                                    <div class="sm:w-32 sm:h-32 h-48 w-full rounded overflow-hidden bg-neutral-surface shrink-0 border border-neutral-border">
                                        <img src="{{ Storage::url($article->cover_image) }}" alt="{{ $article->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    </div>
                                @else
                                    <div class="sm:w-32 sm:h-32 h-36 w-full rounded bg-primary-light border border-red-100 flex items-center justify-center shrink-0 text-primary">
                                        <svg class="w-10 h-10 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                                    </div>
                                @endif
                                <div class="flex-1 flex flex-col justify-between">
                                    <div>
                                        <div class="flex items-center gap-2 text-xs text-neutral-muted mb-1.5">
                                            <span class="font-semibold text-neutral-dark">{{ $article->user->name ?? 'Pustakawan' }}</span>
                                            <span>•</span>
                                            <span>{{ $article->published_at ? $article->published_at->format('d M Y') : $article->created_at->format('d M Y') }}</span>
                                        </div>
                                        <h4 class="text-base font-bold text-neutral-dark group-hover:text-primary transition-colors line-clamp-2 leading-snug">
                                            <a href="{{ route('public.articles.show', $article->slug) }}">
                                                {{ $article->title }}
                                            </a>
                                        </h4>
                                        <p class="text-xs text-neutral-body mt-2 line-clamp-2 leading-relaxed">
                                            {{ $article->excerpt ?? Str::limit(strip_tags($article->content), 100) }}
                                        </p>
                                    </div>
                                    <div class="mt-4 pt-3 border-t border-neutral-border flex justify-end">
                                        <a href="{{ route('public.articles.show', $article->slug) }}" class="text-xs font-semibold text-primary hover:underline flex items-center">
                                            Baca Selengkapnya
                                            <svg class="w-3.5 h-3.5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                        </a>
                                    </div>
                                </div>
                            </article>
                        @empty
                            <div class="p-8 bg-white rounded-lg border border-dashed border-neutral-border text-center space-y-3">
                                <div class="w-10 h-10 rounded-full bg-primary-light text-primary flex items-center justify-center mx-auto">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                                </div>
                                <h4 class="text-sm font-bold text-neutral-dark">Belum Ada Artikel Dipublikasikan</h4>
                                <p class="text-xs text-neutral-muted max-w-sm mx-auto">Artikel ilmiah dan hasil riset pustaka terbaru akan ditampilkan di sini setelah disetujui kurator.</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Esai & Karya Anggota Strip -->
                    <div class="space-y-6">
                        <div class="flex items-center justify-between pb-3 border-b border-neutral-border">
                            <div class="flex items-center space-x-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-accent inline-block"></span>
                                <h3 class="text-lg font-bold text-neutral-dark">Esai & Karya Anggota</h3>
                            </div>
                            <span class="text-xs text-neutral-muted">Terakreditasi Kurator</span>
                        </div>

                        @forelse($publishedEssays as $essay)
                            <article class="p-5 bg-white rounded-lg border border-neutral-border hover:border-accent transition-all duration-200 group flex flex-col justify-between space-y-4 shadow-xs">
                                <div>
                                    <div class="flex items-center justify-between gap-2 mb-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold uppercase tracking-wider bg-accent-light text-neutral-dark border border-[#FDE68A]">
                                            Esai Anggota
                                        </span>
                                        <span class="text-xs text-neutral-muted">
                                            {{ $essay->created_at->format('d M Y') }}
                                        </span>
                                    </div>
                                    <h4 class="text-base font-bold text-neutral-dark group-hover:text-primary transition-colors line-clamp-2 leading-snug">
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
                                    <p class="text-xs text-neutral-body mt-2 line-clamp-3 leading-relaxed">
                                        {{ Str::limit(strip_tags($essay->content ?? 'Esai berbasis dokumen naskah terlampir.'), 140) }}
                                    </p>
                                </div>
                                <div class="pt-3 border-t border-neutral-border flex items-center justify-between text-xs">
                                    <span class="font-medium text-neutral-muted">Oleh: <strong class="text-neutral-dark">{{ $essay->user->name ?? 'Anggota IMM' }}</strong></span>
                                    @auth
                                        <a href="{{ Auth::user()->isAdmin() ? route('admin.essays.show', $essay) : route('anggota.essays.show', $essay) }}" class="font-semibold text-primary hover:underline flex items-center">
                                            Baca Esai
                                            <svg class="w-3.5 h-3.5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                        </a>
                                    @else
                                        <a href="{{ route('login') }}" class="font-semibold text-primary hover:underline flex items-center">
                                            Masuk untuk Membaca
                                            <svg class="w-3.5 h-3.5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                        </a>
                                    @endauth
                                </div>
                            </article>
                        @empty
                            <div class="p-8 bg-white rounded-lg border border-dashed border-neutral-border text-center space-y-3">
                                <div class="w-10 h-10 rounded-full bg-accent-light text-neutral-dark flex items-center justify-center mx-auto border border-[#FDE68A]">
                                    <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002-2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </div>
                                <h4 class="text-sm font-bold text-neutral-dark">Belum Ada Esai Terbit</h4>
                                <p class="text-xs text-neutral-muted max-w-sm mx-auto">Kirimkan gagasan dan karya esai Anda sebagai anggota melalui dasbor keanggotaan.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </section>

        <!-- Library Experience & Research Services -->
        <section id="layanan" class="py-24 bg-white border-b border-neutral-border">
            <div class="max-w-7xl mx-auto px-6">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 items-center">
                    <div class="lg:col-span-6 space-y-6">
                        <span class="text-xs font-semibold uppercase tracking-wider text-primary block">Standar Pelayanan</span>
                        <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-neutral-dark tracking-tight leading-tight">
                            Ruang Sunyi untuk Konsentrasi & Refleksi Mendalam
                        </h2>
                        <p class="text-sm sm:text-base text-neutral-body leading-relaxed">
                            Kami memadukan kenyamanan ruang baca fisik yang hening dengan ketepatan katalog digital. Anggota terdaftar dapat mereservasi buku langsung secara daring dan mengambilnya di loket sirkulasi dalam hitungan menit.
                        </p>

                        <div class="space-y-4 pt-4 border-t border-neutral-border">
                            <div class="flex items-start space-x-3.5">
                                <div class="w-6 h-6 rounded-full bg-green-50 text-green-700 flex items-center justify-center shrink-0 mt-0.5 border border-green-200">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                <div>
                                    <h4 class="text-sm sm:text-base font-semibold text-neutral-dark">Peminjaman Tanpa Hambatan</h4>
                                    <p class="text-xs sm:text-sm text-neutral-body mt-0.5 leading-relaxed">Ajukan peminjaman buku dengan tenggat 14 hari melalui dasbor anggota.</p>
                                </div>
                            </div>

                            <div class="flex items-start space-x-3.5">
                                <div class="w-6 h-6 rounded-full bg-green-50 text-green-700 flex items-center justify-center shrink-0 mt-0.5 border border-green-200">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                <div>
                                    <h4 class="text-sm sm:text-base font-semibold text-neutral-dark">Pustakawan Kurasi Profesional</h4>
                                    <p class="text-xs sm:text-sm text-neutral-body mt-0.5 leading-relaxed">Didukung staf pustaka yang siap mendampingi pencarian sumber referensi.</p>
                                </div>
                            </div>

                            <div class="flex items-start space-x-3.5">
                                <div class="w-6 h-6 rounded-full bg-green-50 text-green-700 flex items-center justify-center shrink-0 mt-0.5 border border-green-200">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                <div>
                                    <h4 class="text-sm sm:text-base font-semibold text-neutral-dark">Akses Riwayat & Notifikasi Tenggat</h4>
                                    <p class="text-xs sm:text-sm text-neutral-body mt-0.5 leading-relaxed">Pantau status pinjaman aktif, tanggal jatuh tempo, dan riwayat sirkulasi secara transparan.</p>
                                </div>
                            </div>
                        </div>

                        <div class="pt-4">
                            <a href="{{ route('register') }}" class="btn-editorial text-xs sm:text-sm py-3 px-6 uppercase tracking-wider font-semibold">
                                Bergabung Sebagai Anggota RPK PUSTAKA IMM SAINTEKMU
                            </a>
                        </div>
                    </div>

                    <div class="lg:col-span-6">
                        <div class="bg-[#F8F8F7] border border-neutral-border p-8 rounded-lg space-y-6">
                            <div class="border-b border-neutral-border pb-6">
                                <span class="text-xs font-semibold uppercase tracking-wider text-primary">Pedoman Umum Sirkulasi</span>
                                <h3 class="text-xl sm:text-2xl font-bold text-neutral-dark mt-1">Ketentuan Keanggotaan</h3>
                            </div>

                            <div class="space-y-4 text-xs sm:text-sm text-neutral-body leading-relaxed">
                                <div class="flex items-baseline justify-between border-b border-neutral-border pb-3">
                                    <span class="font-semibold text-neutral-dark">Batas Waktu Pengembalian:</span>
                                    <span>14 Hari Kalender (Dapat Diperpanjang)</span>
                                </div>
                                <div class="flex items-baseline justify-between border-b border-neutral-border pb-3">
                                    <span class="font-semibold text-neutral-dark">Denda Keterlambatan:</span>
                                    <span>Dihitung Otomatis Berdasarkan Aturan Harian</span>
                                </div>
                                <div class="flex items-baseline justify-between pb-1">
                                    <span class="font-semibold text-neutral-dark">Kondisi Buku:</span>
                                    <span>Wajib Dijaga Kebersihan & Keutuhan Halaman</span>
                                </div>
                            </div>

                            <div class="bg-white p-4 rounded border border-neutral-border text-xs sm:text-sm text-neutral-body italic">
                                "Menjaga buku sama halnya menghormati ribuan pemikiran yang mendahului kita."
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Dignified Institution Footer -->
        <footer id="tentang" class="bg-[#181818] text-[#E5E5E5] pt-16 pb-12 border-t border-[#262626]">
            <div class="max-w-7xl mx-auto px-6">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-12 pb-14 border-b border-[#262626]">
                    <!-- Brand Column -->
                    <div class="md:col-span-5 space-y-4">
                        <div class="flex items-center space-x-3">
                            <img src="{{ asset('images/logo-rpk.png') }}" alt="RPK PUSTAKA IMM SAINTEKMU" class="h-10 w-auto object-contain bg-white rounded p-1">
                            <div>
                                <span class="text-xl font-bold tracking-tight text-white block leading-none whitespace-nowrap">RPK PUSTAKA</span>
                                <span class="text-[10px] uppercase tracking-wider text-[#A3A3A3] font-semibold block mt-1 whitespace-nowrap">IMM SAINTEKMU</span>
                            </div>
                        </div>
                        <p class="text-xs sm:text-[13px] text-[#A3A3A3] leading-relaxed max-w-sm pt-2">
                            Lembaga perpustakaan riset dan dokumentasi ilmiah. Didedikasikan untuk memelihara warisan pemikiran dan menyediakan akses setara terhadap ilmu pengetahuan bagi civitas akademika.
                        </p>
                        <div class="flex items-center gap-2 pt-2">
                            <svg class="w-3.5 h-3.5 text-accent fill-current" viewBox="0 0 24 24">
                                <path d="M12 2l2.4 7.2h7.6l-6.2 4.5 2.4 7.3-6.2-4.6-6.2 4.6 2.4-7.3-6.2-4.5h7.6z"/>
                            </svg>
                            <p class="italic text-xs sm:text-[13px] text-white font-medium">
                                Mencerahkan Generasi Melalui Akses Pengetahuan
                            </p>
                        </div>
                    </div>

                    <!-- Navigation Links -->
                    <div class="md:col-span-3 space-y-3">
                        <h4 class="text-xs sm:text-[13px] font-semibold uppercase tracking-wider text-white">Navigasi Utama</h4>
                        <ul class="space-y-2.5 text-xs sm:text-[13px] text-[#A3A3A3]">
                            <li><a href="#koleksi" class="hover:text-white transition-colors">Katalog Buku Pilihan</a></li>
                            <li><a href="#kategori" class="hover:text-white transition-colors">Kategori Koleksi Buku</a></li>
                            @auth
                                <li><a href="{{ url('/dashboard') }}" class="hover:text-white transition-colors">{{ Auth::user()->isAdmin() ? 'Dasbor Admin' : 'Dasbor Anggota' }}</a></li>
                            @else
                                <li><a href="{{ route('login') }}" class="hover:text-white transition-colors">Masuk ke Portal Anggota</a></li>
                                <li><a href="{{ route('register') }}" class="hover:text-white transition-colors">Pendaftaran Anggota Baru</a></li>
                            @endauth
                        </ul>
                    </div>

                    <!-- Operational Hours -->
                    <div class="md:col-span-4 space-y-3">
                        <h4 class="text-xs sm:text-[13px] font-semibold uppercase tracking-wider text-white">Jam Layanan Sirkulasi</h4>
                        <div class="space-y-2 text-xs sm:text-[13px] text-[#A3A3A3]">
                            <div class="flex justify-between border-b border-[#262626] pb-1.5">
                                <span>Senin — Jumat:</span>
                                <span class="text-white font-medium">06:00 – 00:00 WIB</span>
                            </div>
                            <div class="flex justify-between pt-1">
                                <span>Sabtu, Minggu & Hari Libur:</span>
                                <span class="text-[#737373] italic">Tutup (Layanan Digital 24 Jam)</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Copyright Bar -->
                <div class="pt-8 flex flex-col sm:flex-row items-center justify-between text-xs text-[#888888] gap-4">
                    <p>&copy; {{ date('Y') }} RPK PUSTAKA IMM SAINTEKMU — Hak Cipta Dilindungi. Sesuai Standar Tata Kelola Perpustakaan Digital Nasional.</p>
                    <p class="font-medium">IMM SAINTEKMU</p>
                </div>
            </div>
        </footer>

        <script>
            window.initialBooks = @json($books);
        </script>
    </body>
</html>
