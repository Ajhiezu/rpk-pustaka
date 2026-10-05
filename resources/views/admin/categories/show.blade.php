<x-app-layout>
    <x-slot name="header">
        Koleksi Buku Kategori: {{ $category->name }}
    </x-slot>

    <x-slot name="actions">
        <a href="{{ route('admin.categories.index') }}" class="btn-editorial-outline text-xs py-2 px-3.5 uppercase tracking-wider inline-flex items-center">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Kategori
        </a>
        <a href="{{ route('admin.categories.edit', $category) }}" class="btn-editorial text-xs py-2 px-3.5 uppercase tracking-wider inline-flex items-center shadow-xs">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            Ubah Kategori
        </a>
    </x-slot>

    <div class="space-y-6 animate-in fade-in duration-300">
        
        <!-- Category Identity Banner Card -->
        <div class="bg-white p-5 rounded-lg border border-neutral-border shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="space-y-1.5">
                <nav class="flex items-center gap-2 text-[11px] text-neutral-muted uppercase tracking-wider font-mono">
                    <a href="{{ route('dashboard') }}" class="hover:text-primary transition-colors">Dasbor</a>
                    <span>/</span>
                    <a href="{{ route('admin.categories.index') }}" class="hover:text-primary transition-colors">Kategori Buku</a>
                    <span>/</span>
                    <span class="text-primary font-bold">{{ $category->name }}</span>
                </nav>
                <div class="flex flex-wrap items-center gap-3">
                    <h2 class="text-xl sm:text-2xl font-bold text-neutral-dark tracking-tight leading-snug">
                        {{ $category->name }}
                    </h2>
                    <code class="text-xs font-mono text-neutral-body bg-neutral-surface px-2.5 py-1 rounded border border-neutral-border">
                        slug: {{ $category->slug }}
                    </code>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-4 text-xs font-mono text-neutral-muted pt-3 sm:pt-0 border-t sm:border-t-0 border-neutral-border">
                <div>
                    <span class="text-neutral-muted block text-[10px] uppercase tracking-wider">Total Koleksi</span>
                    <span class="font-bold text-neutral-dark text-sm">{{ $books->total() }} Judul</span>
                </div>
            </div>
        </div>

        <!-- Search Toolbar -->
        <div class="bg-white p-4 rounded-lg border border-neutral-border shadow-xs">
            <form action="{{ route('admin.categories.show', $category) }}" method="GET" class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1 relative">
                    <svg class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-neutral-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul buku, penulis, ISBN, atau kode buku dalam kategori ini..." 
                        class="w-full pl-10 pr-4 py-2 bg-neutral-surface border border-neutral-border rounded-md text-sm text-neutral-dark placeholder:text-neutral-muted focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                </div>
                <div class="flex space-x-2">
                    <x-button type="submit" variant="primary" class="px-5 text-xs uppercase tracking-wider font-semibold">Cari</x-button>
                    @if(request('search'))
                        <a href="{{ route('admin.categories.show', $category) }}" class="btn-editorial-outline px-4 text-xs uppercase tracking-wider font-semibold">Reset</a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Books Table View -->
        <div class="space-y-3">
            <div class="flex items-center justify-between px-1">
                <span class="text-xs font-semibold uppercase tracking-wider text-neutral-dark">
                    Daftar Koleksi Buku Terkait ({{ $books->total() }} Judul)
                </span>
            </div>

            <x-table :headers="['Koleksi Buku', 'Penempatan Lokasi Rak', 'Tipe Koleksi', 'Stok Buku', 'Aksi']">
                @forelse($books as $book)
                    <tr class="hover:bg-neutral-surface transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-start space-x-4">
                                <div class="w-12 h-16 bg-[#F8F8F7] border border-neutral-border rounded overflow-hidden flex-shrink-0 flex items-center justify-center shadow-xs">
                                    @if($book->image)
                                        <img src="{{ asset('storage/'.$book->image) }}" alt="{{ $book->title }}" class="w-full h-full object-cover">
                                    @else
                                        <svg class="w-6 h-6 text-neutral-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                        </svg>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2 mb-0.5">
                                        <span class="text-[10px] font-mono font-bold text-primary bg-primary-light px-1.5 py-0.5 rounded border border-red-100">
                                            {{ $book->book_code ?? 'CODE' }}
                                        </span>
                                        @if($book->isbn)
                                            <span class="text-[10px] font-mono text-neutral-muted">ISBN: {{ $book->isbn }}</span>
                                        @endif
                                    </div>
                                    <a href="{{ route('admin.books.show', $book) }}" class="text-sm font-bold text-neutral-dark hover:text-primary transition-colors block leading-tight">
                                        {{ $book->title }}
                                    </a>
                                    <p class="text-xs text-neutral-body mt-1">
                                        Oleh <span class="font-medium text-neutral-dark">{{ $book->author }}</span>
                                        @if($book->year)
                                            ({{ $book->year }})
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </td>

                        <!-- Lokasi Rak Column (Highlighted) -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($book->location)
                                <a href="{{ route('admin.locations.show', $book->location) }}" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md bg-emerald-50 text-emerald-800 border border-emerald-200 hover:bg-emerald-100 transition-colors group">
                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    </svg>
                                    <span class="text-xs font-bold font-mono">{{ $book->location->name }}</span>
                                </a>
                                @if($book->location->description)
                                    <span class="block text-[10px] text-neutral-muted mt-1 truncate max-w-[180px]">
                                        {{ $book->location->description }}
                                    </span>
                                @endif
                            @else
                                <span class="text-xs text-neutral-muted italic font-mono">Belum Ditempatkan</span>
                            @endif
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($book->collection_type === 'fisik_digital')
                                <span class="inline-flex items-center px-2.5 py-0.5 text-[10px] font-bold bg-primary-light text-primary border border-red-200 rounded">
                                    Fisik & Digital
                                </span>
                            @elseif($book->collection_type === 'digital')
                                <span class="inline-flex items-center px-2.5 py-0.5 text-[10px] font-bold bg-[#FFF9ED] text-[#B45309] border border-[#FDE68A] rounded">
                                    Digital
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 text-[10px] font-bold bg-[#EDF7ED] text-success border border-[#C8E6C9] rounded">
                                    Fisik
                                </span>
                            @endif
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-xs font-mono">
                                <span class="font-bold {{ $book->available_stock > 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $book->available_stock }}
                                </span>
                                <span class="text-neutral-muted">/ {{ $book->stock }} Stok</span>
                            </div>
                        </td>

                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <div class="flex justify-end space-x-1.5">
                                <a href="{{ route('admin.books.show', $book) }}" class="p-1.5 text-neutral-body hover:text-primary hover:bg-primary-light rounded transition-colors" title="Lihat Detail Buku">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                <a href="{{ route('admin.books.edit', $book) }}" class="p-1.5 text-neutral-body hover:text-primary hover:bg-primary-light rounded transition-colors" title="Ubah Data Buku">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-neutral-muted text-xs italic">
                            Belum ada koleksi buku yang tergolong dalam kategori <strong>"{{ $category->name }}"</strong>.
                        </td>
                    </tr>
                @endforelse
            </x-table>
        </div>

        <div class="mt-6 pt-4 border-t border-neutral-border">
            {{ $books->links() }}
        </div>
    </div>
</x-app-layout>
