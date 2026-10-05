<x-app-layout>
    <x-slot name="header">
        Kelola Koleksi Buku — RPK PUSTAKA IMM SAINTEK MU
    </x-slot>

    <x-slot name="actions">
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.books.import.create') }}" class="btn-editorial-outline text-xs py-2 px-4 shadow-xs flex items-center">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                Import Massal
            </a>
            <a href="{{ route('admin.books.create') }}" class="btn-editorial text-xs py-2 px-4 shadow-xs flex items-center">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Koleksi Baru
            </a>
        </div>
    </x-slot>

    <div class="space-y-6 animate-in fade-in duration-300">
        <!-- Toolbar, Search, & Format Filter -->
        <div class="bg-white p-4 rounded-lg border border-neutral-border shadow-xs space-y-3">
            <form action="{{ route('admin.books.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1 relative">
                    <svg class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-neutral-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari naskah berdasarkan judul, penulis, atau nomor ISBN..." 
                        class="w-full pl-10 pr-4 py-2 bg-neutral-surface border border-neutral-border rounded-md text-sm text-neutral-dark placeholder:text-neutral-muted focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                </div>
                @if(request('format'))
                    <input type="hidden" name="format" value="{{ request('format') }}">
                @endif
                <div class="flex space-x-2">
                    <x-button type="submit" variant="primary" class="px-5 text-xs uppercase tracking-wider font-semibold">Cari</x-button>
                    @if(request('search') || request('format'))
                        <a href="{{ route('admin.books.index') }}" class="btn-editorial-outline px-4 text-xs uppercase tracking-wider font-semibold">Reset</a>
                    @endif
                </div>
            </form>

            <div class="flex items-center gap-2 pt-2 border-t border-neutral-border text-xs">
                <span class="text-[11px] font-semibold text-neutral-muted uppercase tracking-wider mr-1">Filter Format:</span>
                <a href="{{ route('admin.books.index', ['search' => request('search')]) }}" 
                   class="px-2.5 py-1 rounded text-xs font-semibold {{ !request('format') ? 'bg-neutral-dark text-white' : 'bg-neutral-surface text-neutral-body hover:bg-neutral-border' }}">
                    Semua
                </a>
                <a href="{{ route('admin.books.index', ['format' => 'digital', 'search' => request('search')]) }}" 
                   class="px-2.5 py-1 rounded text-xs font-semibold flex items-center gap-1 {{ request('format') === 'digital' ? 'bg-primary text-white' : 'bg-[#FFF9ED] text-[#B45309] border border-[#FDE68A]' }}">
                    Digital
                </a>
                <a href="{{ route('admin.books.index', ['format' => 'physical', 'search' => request('search')]) }}" 
                   class="px-2.5 py-1 rounded text-xs font-semibold flex items-center gap-1 {{ request('format') === 'physical' ? 'bg-primary text-white' : 'bg-[#EDF7ED] text-success border border-[#C8E6C9]' }}">
                    Fisik
                </a>
            </div>
        </div>

        <!-- Academic Table View -->
        <x-table :headers="['Koleksi Pustaka', 'Klasifikasi', 'Format Koleksi', 'Stok Fisik', 'Lokasi Rak', 'Aksi']">
            @forelse($books as $book)
                <tr class="group transition-colors hover:bg-neutral-surface">
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-3.5">
                            <div class="w-10 h-14 bg-neutral-surface rounded overflow-hidden shrink-0 border border-neutral-border shadow-xs flex items-center justify-center">
                                @if($book->cover_url)
                                    <img src="{{ $book->cover_url }}" alt="{{ $book->title }}" class="w-full h-full object-cover" loading="lazy">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-primary">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                    </div>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <a href="{{ route('admin.books.show', $book) }}" class="font-sans text-sm font-semibold text-neutral-dark hover:text-primary transition-colors block leading-tight truncate max-w-[280px]">
                                    {{ $book->title }}
                                </a>
                                <p class="text-[11px] text-neutral-body mt-0.5">
                                    <span class="font-mono font-bold text-primary text-[10px]">{{ $book->book_code ?? '-' }}</span> • 
                                    <span>{{ $book->author }}</span> • 
                                    <span class="font-mono text-[10px] text-neutral-muted">ISBN: {{ $book->isbn ?? '-' }}</span>
                                </p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <x-badge variant="indigo">{{ $book->category->name ?? 'Umum' }}</x-badge>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($book->collection_type === 'fisik_digital')
                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-primary bg-primary-light border border-red-200 px-2 py-0.5 rounded">
                                Fisik & Digital
                            </span>
                        @elseif($book->collection_type === 'digital')
                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-[#B45309] bg-[#FFF9ED] border border-[#FDE68A] px-2 py-0.5 rounded">
                                Digital
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-success bg-[#EDF7ED] border border-[#C8E6C9] px-2 py-0.5 rounded">
                                Fisik
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($book->collection_type === 'digital')
                            <span class="text-xs text-neutral-muted italic">- (Digital Only)</span>
                        @else
                            <div class="flex flex-col">
                                <span class="text-xs font-bold text-neutral-dark">{{ $book->available_stock }} <span class="text-[10px] text-success font-semibold">Tersedia</span></span>
                                <span class="text-[10px] text-neutral-muted">dari {{ $book->stock }} Total Fisik</span>
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-[10px] font-semibold text-neutral-body uppercase tracking-wider bg-neutral-surface border border-neutral-border px-2.5 py-1 rounded inline-block">
                            {{ $book->location->name ?? '-' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center space-x-1.5">
                            <a href="{{ route('admin.books.show', $book) }}" 
                               class="p-1.5 text-neutral-body hover:text-primary hover:bg-primary-light rounded transition-colors" 
                               title="Lihat Detail Buku">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </a>
                            @if($book->hasDigital())
                                <a href="{{ route('admin.books.reader', $book) }}" 
                                   target="_blank"
                                   class="p-1.5 text-accent hover:text-amber-800 hover:bg-[#FFF9ED] rounded transition-colors" 
                                   title="Baca Naskah Digital (PDF)">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                </a>
                            @endif
                            <a href="{{ route('admin.books.edit', $book) }}" 
                               class="p-1.5 text-neutral-body hover:text-primary hover:bg-primary-light rounded transition-colors" 
                               title="Edit Data Buku">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </a>
                            <form action="{{ route('admin.books.destroy', $book) }}" method="POST" class="inline" data-confirm-message="Apakah Anda yakin ingin menghapus buku '{{ $book->title }}' dari katalog perpustakaan?">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="p-1.5 text-neutral-muted hover:text-danger hover:bg-red-50 rounded transition-colors cursor-pointer"
                                        title="Hapus Koleksi">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-xs text-neutral-muted italic">
                        Belum ada koleksi buku yang terdaftar.
                    </td>
                </tr>
            @endforelse
        </x-table>

        <div class="pt-4">
            {{ $books->links() }}
        </div>
    </div>
</x-app-layout>
