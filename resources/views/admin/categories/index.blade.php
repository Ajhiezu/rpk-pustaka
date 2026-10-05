<x-app-layout>
    <x-slot name="header">
        Manajemen Kategori Buku
    </x-slot>

    <x-slot name="actions">
        <a href="{{ route('admin.categories.create') }}" class="btn-editorial text-xs py-2 px-4 shadow-xs">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Tambah Kategori Baru
        </a>
    </x-slot>

    <div class="space-y-6 animate-in fade-in duration-300">
        <!-- Search Toolbar -->
        <div class="bg-white p-4 rounded-lg border border-neutral-border shadow-xs">
            <form action="{{ route('admin.categories.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1 relative">
                    <svg class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-neutral-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama kategori..." 
                        class="w-full pl-10 pr-4 py-2 bg-neutral-surface border border-neutral-border rounded-md text-sm text-neutral-dark placeholder:text-neutral-muted focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                </div>
                <div class="flex space-x-2">
                    <x-button type="submit" variant="primary" class="px-5 text-xs uppercase tracking-wider font-semibold">Cari</x-button>
                    @if(request('search'))
                        <a href="{{ route('admin.categories.index') }}" class="btn-editorial-outline px-4 text-xs uppercase tracking-wider font-semibold">Reset</a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table View -->
        <div class="space-y-3">
            <div class="flex items-center justify-between px-1">
                <span class="text-xs font-semibold uppercase tracking-wider text-neutral-dark">
                    Total Terdata: {{ $categories->total() }} Kategori
                </span>
            </div>

            <x-table :headers="['Nama Kategori', 'Slug Sistem', 'Jumlah Koleksi', 'Aksi']">
                @forelse($categories as $category)
                    <tr class="hover:bg-neutral-surface transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center space-x-3.5">
                                <div class="w-9 h-9 bg-primary-light border border-primary/20 text-primary font-bold text-xs rounded-md flex items-center justify-center shrink-0 shadow-xs">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <a href="{{ route('admin.categories.show', $category) }}" class="text-sm font-semibold text-neutral-dark hover:text-primary transition-colors block leading-tight">
                                        {{ $category->name }}
                                    </a>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <code class="text-xs font-mono text-neutral-body bg-neutral-surface px-2.5 py-1 rounded border border-neutral-border">{{ $category->slug }}</code>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="{{ route('admin.categories.show', $category) }}" class="inline-flex items-center gap-1 group">
                                <x-badge variant="indigo">{{ $category->books()->count() }} Koleksi</x-badge>
                            </a>
                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <div class="flex justify-end space-x-1.5">
                                <a href="{{ route('admin.categories.show', $category) }}" class="p-1.5 text-neutral-body hover:text-primary hover:bg-primary-light rounded transition-colors" title="Lihat Koleksi Buku di Kategori Ini">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                <a href="{{ route('admin.categories.edit', $category) }}" class="p-1.5 text-neutral-body hover:text-primary hover:bg-primary-light rounded transition-colors" title="Ubah Kategori">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                    </svg>
                                </a>
                                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline" data-confirm-message="Apakah Anda yakin ingin menghapus kategori '{{ $category->name }}'? Data yang terhapus tidak dapat dipulihkan.">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-neutral-muted hover:text-danger hover:bg-red-50 rounded transition-colors cursor-pointer" title="Hapus Kategori">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-neutral-muted text-xs italic">
                            Belum ada kategori buku ditemukan.
                        </td>
                    </tr>
                @endforelse
            </x-table>
        </div>

        <div class="mt-6 pt-4 border-t border-neutral-border">
            {{ $categories->links() }}
        </div>
    </div>
</x-app-layout>
