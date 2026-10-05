<x-app-layout>
    <x-slot name="header">
        Eksplorasi Katalog Koleksi — RPK PUSTAKA IMM SAINTEK MU
    </x-slot>

    <div class="space-y-10 animate-in fade-in duration-300">
        <!-- Editorial Section Lead -->
        <div class="space-y-1.5">
            <span class="text-xs uppercase tracking-[0.2em] font-semibold text-primary block">Katalog Pustaka Digital & Fisik</span>
            <h2 class="font-sans text-2xl sm:text-3xl font-bold text-neutral-dark tracking-tight">Koleksi Buku & Manuskrip</h2>
            <p class="text-xs sm:text-sm text-neutral-body">Akses seluruh koleksi literatur fisik di rak perpustakaan dan buku digital (e-book PDF) yang siap dipinjam secara langsung.</p>
        </div>

        <!-- Prominent Academic Search & Filter Area -->
        <div class="bg-white p-5 rounded-lg border border-neutral-border shadow-xs space-y-4">
            <form action="{{ route('anggota.books.index') }}" method="GET" class="space-y-4">
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="flex-1 relative">
                        <svg class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-neutral-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <input 
                            type="text" 
                            name="search" 
                            value="{{ request('search') }}" 
                            placeholder="Cari berdasarkan judul naskah, nama penulis, atau ISBN..." 
                            class="w-full pl-10 pr-4 py-2.5 bg-white border border-neutral-border rounded-md text-sm text-neutral-dark placeholder:text-neutral-muted focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                        >
                    </div>

                    @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    @if(request('format'))
                        <input type="hidden" name="format" value="{{ request('format') }}">
                    @endif

                    <div class="flex items-center space-x-2">
                        <x-button type="submit" variant="primary" class="px-5 py-2.5 text-xs uppercase tracking-wider font-semibold">
                            Cari Koleksi
                        </x-button>
                        @if(request('search') || request('category') || request('format'))
                            <a href="{{ route('anggota.books.index') }}" class="btn-editorial-outline px-4 py-2.5 text-xs uppercase tracking-wider font-semibold">
                                Reset
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Format Filter Pills -->
                <div class="pt-3 border-t border-neutral-border flex flex-wrap items-center gap-2">
                    <span class="text-[11px] font-semibold text-neutral-muted uppercase tracking-wider shrink-0 mr-1">Format:</span>
                    
                    <a href="{{ route('anggota.books.index', array_merge(request()->except('format'), [])) }}"
                       class="px-3 py-1 text-xs font-semibold rounded transition-colors shrink-0 {{ !request('format') ? 'bg-neutral-dark text-white' : 'bg-[#F8F8F7] text-neutral-body border border-neutral-border hover:border-neutral-dark hover:text-neutral-dark' }}">
                        Semua Format
                    </a>

                    <a href="{{ route('anggota.books.index', array_merge(request()->except('format'), ['format' => 'digital'])) }}"
                       class="px-3 py-1 text-xs font-semibold rounded transition-colors shrink-0 flex items-center gap-1.5 {{ request('format') === 'digital' ? 'bg-primary text-white' : 'bg-[#FFF9ED] text-[#B45309] border border-[#FDE68A] hover:bg-primary-light hover:text-primary' }}">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        Buku Digital (PDF)
                    </a>

                    <a href="{{ route('anggota.books.index', array_merge(request()->except('format'), ['format' => 'physical'])) }}"
                       class="px-3 py-1 text-xs font-semibold rounded transition-colors shrink-0 flex items-center gap-1.5 {{ request('format') === 'physical' ? 'bg-primary text-white' : 'bg-[#EDF7ED] text-success border border-[#C8E6C9] hover:bg-primary-light hover:text-primary' }}">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path></svg>
                        Buku Cetak Fisik
                    </a>
                </div>

                <!-- Academic Category Chips -->
                <div class="pt-2 border-t border-neutral-border flex items-center gap-2 overflow-x-auto custom-scrollbar pb-1">
                    <span class="text-[11px] font-semibold text-neutral-muted uppercase tracking-wider shrink-0 mr-1">Kategori:</span>
                    <a href="{{ route('anggota.books.index', array_merge(request()->except('category'), [])) }}" 
                       class="px-3 py-1 text-xs font-semibold rounded transition-colors shrink-0 {{ !request('category') ? 'bg-primary text-white' : 'bg-[#F8F8F7] text-neutral-body border border-neutral-border hover:border-primary hover:text-primary' }}">
                        Semua Kategori
                    </a>
                    @foreach($categories ?? [] as $category)
                        <a href="{{ route('anggota.books.index', array_merge(request()->except('category'), ['category' => $category->slug])) }}" 
                           class="px-3 py-1 text-xs font-semibold rounded transition-colors shrink-0 {{ request('category') == $category->slug ? 'bg-primary text-white' : 'bg-[#F8F8F7] text-neutral-body border border-neutral-border hover:border-primary hover:text-primary' }}">
                            {{ $category->name }}
                        </a>
                    @endforeach
                </div>
            </form>
        </div>

        <!-- Book Grid: Editorial Layout -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-7">
            @forelse($books as $book)
                <article class="group flex flex-col bg-white border border-neutral-border rounded-lg overflow-hidden transition-all duration-300 hover:border-primary/50 hover:shadow-md">
                    <!-- Book Cover -->
                    <div class="w-full aspect-[3/4.2] bg-[#F8F8F7] overflow-hidden relative border-b border-neutral-border">
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
                                <span class="font-sans text-sm font-semibold text-neutral-dark line-clamp-2">{{ $book->title }}</span>
                                <span class="text-[10px] text-neutral-muted mt-1">{{ $book->author }}</span>
                            </div>
                        @endif
                        
                        <!-- Top Metadata Badges -->
                        <div class="absolute top-3 left-3">
                            <span class="bg-white/95 backdrop-blur-xs px-2.5 py-0.5 text-[10px] font-bold text-primary uppercase tracking-wider rounded border border-neutral-border shadow-xs">
                                {{ $book->category->name }}
                            </span>
                        </div>

                        <!-- Format Availability Badge -->
                        <div class="absolute top-3 right-3 flex flex-col items-end gap-1">
                            @if($book->collection_type === 'fisik_digital')
                                <span class="px-2 py-0.5 bg-primary text-white text-[9px] font-bold uppercase tracking-wider rounded shadow-xs">
                                    FISIK + DIGITAL
                                </span>
                            @elseif($book->collection_type === 'digital')
                                <span class="px-2 py-0.5 bg-[#FFF9ED] text-[#B45309] text-[9px] font-bold uppercase tracking-wider rounded border border-[#FDE68A] shadow-xs">
                                    DIGITAL
                                </span>
                            @else
                                <span class="px-2 py-0.5 bg-[#EDF7ED] text-success text-[9px] font-bold uppercase tracking-wider rounded border border-[#C8E6C9] shadow-xs">
                                    FISIK
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Content Hierarchy -->
                    <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
                        <div class="space-y-1">
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-xs text-neutral-muted font-medium truncate">{{ $book->author }}</p>
                                <span class="font-mono text-[10px] text-neutral-muted font-semibold shrink-0">{{ $book->book_code ?? '' }}</span>
                            </div>
                            <h3 class="font-sans text-base font-semibold text-neutral-dark leading-snug group-hover:text-primary transition-colors line-clamp-2">
                                <a href="{{ route('anggota.books.show', $book) }}">
                                    {{ $book->title }}
                                </a>
                            </h3>
                        </div>

                        <!-- Availability & Location Footer -->
                        <div class="pt-3 border-t border-neutral-border flex flex-col gap-2.5">
                            <div class="flex items-center justify-between gap-2 text-xs">
                                <span class="text-[11px] text-neutral-muted">Ketersediaan</span>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    @if($book->collection_type === 'digital')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-[#FFF9ED] text-[#B45309] border border-[#FDE68A]">
                                            Digital PDF
                                        </span>
                                    @elseif($book->collection_type === 'fisik_digital')
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-[#FFF9ED] text-[#B45309] border border-[#FDE68A]">
                                            Digital
                                        </span>
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold {{ $book->available_stock > 0 ? 'bg-[#EDF7ED] text-success border border-[#C8E6C9]' : 'bg-red-50 text-danger border border-red-200' }}">
                                            {{ $book->available_stock > 0 ? $book->available_stock.' Eks' : 'Habis' }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold {{ $book->available_stock > 0 ? 'bg-[#EDF7ED] text-success border border-[#C8E6C9]' : 'bg-red-50 text-danger border border-red-200' }}">
                                            {{ $book->available_stock > 0 ? $book->available_stock.' Eks Fisik' : 'Habis Dipinjam' }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center justify-between pt-1 text-xs">
                                <div class="flex items-center gap-1.5 text-neutral-body text-[11px] min-w-0">
                                    @if($book->collection_type === 'digital')
                                        <svg class="w-3.5 h-3.5 text-[#B45309] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                        </svg>
                                        <span class="truncate text-neutral-muted font-mono text-[10px]">Akses Digital</span>
                                    @else
                                        <svg class="w-3.5 h-3.5 text-neutral-muted shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                        </svg>
                                        <span class="truncate font-mono text-[10px] text-neutral-muted">{{ $book->location->name ?? 'Rak Utama' }}</span>
                                    @endif
                                </div>

                                <a href="{{ route('anggota.books.show', $book) }}" class="inline-flex items-center gap-1 font-bold text-xs text-primary hover:text-primary-dark transition-colors shrink-0 group-hover:translate-x-0.5 transition-transform">
                                    <span>Buka Detail</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </article>
            @empty
                <div class="col-span-full py-20 text-center bg-white rounded-md border border-neutral-border">
                    <div class="w-12 h-12 rounded-full bg-primary-light text-primary flex items-center justify-center mx-auto mb-3 border border-red-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 9.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <p class="font-sans text-lg font-bold text-neutral-dark">Koleksi Tidak Ditemukan</p>
                    <p class="text-xs text-neutral-muted mt-1 max-w-sm mx-auto">Tidak ada buku yang sesuai dengan kata kunci, kategori, atau filter format yang dipilih.</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-10 pt-6 border-t border-neutral-border">
            {{ $books->links() }}
        </div>
    </div>
</x-app-layout>
