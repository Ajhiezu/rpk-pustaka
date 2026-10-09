<x-app-layout>
    <x-slot name="header">
        Manajemen Lokasi Rak Buku
    </x-slot>

    <x-slot name="actions">
        <a href="{{ route('admin.locations.create') }}" class="btn-editorial text-xs py-2 px-4 shadow-xs">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Tambah Lokasi Rak
        </a>
    </x-slot>

    <div class="space-y-6 animate-in fade-in duration-300">
        <!-- Search Toolbar -->
        <div class="bg-white p-4 rounded-lg border border-neutral-border shadow-xs">
            <form action="{{ route('admin.locations.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1 relative">
                    <svg class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-neutral-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama rak atau deskripsi posisi..." 
                        class="w-full pl-10 pr-4 py-2 bg-neutral-surface border border-neutral-border rounded-md text-sm text-neutral-dark placeholder:text-neutral-muted focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                </div>
                <div class="flex space-x-2">
                    <x-button type="submit" variant="primary" class="px-5 text-xs uppercase tracking-wider font-semibold">Cari</x-button>
                    @if(request('search'))
                        <a href="{{ route('admin.locations.index') }}" class="btn-editorial-outline px-4 text-xs uppercase tracking-wider font-semibold">Reset</a>
                    @endif
                </div>
            </form>
        </div>

        <!-- ============================================== -->
        <!-- MOBILE CARD VIEW (Tampil khusus layar HP/Tablet) -->
        <!-- ============================================== -->
        <div class="block md:hidden space-y-3">
            @forelse($locations as $location)
                <div class="bg-white rounded-lg border border-neutral-border shadow-xs overflow-hidden">
                    <div class="p-4 flex items-center justify-between gap-3">
                        <div class="flex items-center space-x-3 min-w-0">
                            <div class="w-10 h-10 bg-emerald-50 border border-emerald-200 text-emerald-700 font-bold text-xs rounded-md flex items-center justify-center shrink-0 shadow-xs">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <a href="{{ route('admin.locations.show', $location) }}" class="text-sm font-bold text-neutral-dark hover:text-primary transition-colors block leading-tight truncate">
                                    {{ $location->name }}
                                </a>
                                <p class="text-[11px] text-neutral-body mt-0.5 truncate">
                                    {{ $location->description ?: 'Tanpa keterangan posisi' }}
                                </p>
                            </div>
                        </div>
                        <div class="shrink-0">
                            <x-badge variant="emerald">{{ $location->books()->count() }} Koleksi</x-badge>
                        </div>
                    </div>

                    <!-- Bottom Action Bar -->
                    <div class="bg-neutral-surface px-3.5 py-2.5 border-t border-neutral-border flex flex-wrap items-center justify-between gap-2">
                        <div class="flex flex-wrap items-center gap-1.5">
                            <a href="{{ route('admin.locations.show', $location) }}" 
                               class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-white border border-neutral-border rounded text-xs font-semibold text-neutral-dark hover:text-primary hover:border-primary transition-colors shadow-2xs">
                                <svg class="w-3.5 h-3.5 text-neutral-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                <span>Lihat Rak</span>
                            </a>
                            <a href="{{ route('admin.locations.edit', $location) }}" 
                               class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-white border border-neutral-border rounded text-xs font-semibold text-neutral-dark hover:text-primary hover:border-primary transition-colors shadow-2xs">
                                <svg class="w-3.5 h-3.5 text-neutral-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                <span>Edit</span>
                            </a>
                        </div>
                        <form action="{{ route('admin.locations.destroy', $location) }}" method="POST" class="inline shrink-0" data-confirm-message="Apakah Anda yakin ingin menghapus lokasi rak '{{ $location->name }}'? Data yang terhapus tidak dapat dipulihkan.">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-8 h-8 inline-flex items-center justify-center p-1.5 text-neutral-muted hover:text-danger hover:bg-red-50 rounded transition-colors cursor-pointer" title="Hapus Lokasi Rak">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="bg-white p-8 rounded-lg border border-neutral-border text-center text-xs text-neutral-muted italic">
                    Belum ada lokasi rak terdaftar.
                </div>
            @endforelse
        </div>

        <!-- ============================================== -->
        <!-- DESKTOP TABLE VIEW (Tampil di Laptop & PC)      -->
        <!-- ============================================== -->
        <div class="hidden md:block">
            <x-table :headers="['Nama / Kode Rak', 'Keterangan Posisi', 'Jumlah Koleksi', 'Aksi']">
                @forelse($locations as $location)
                    <tr class="hover:bg-neutral-surface transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center space-x-3.5">
                                <div class="w-9 h-9 bg-emerald-50 border border-emerald-200 text-emerald-700 font-bold text-xs rounded-md flex items-center justify-center shrink-0 shadow-xs">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <a href="{{ route('admin.locations.show', $location) }}" class="text-sm font-semibold text-neutral-dark hover:text-primary transition-colors block leading-tight">
                                        {{ $location->name }}
                                    </a>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs text-neutral-body">{{ $location->description ?: '-' }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="{{ route('admin.locations.show', $location) }}" class="inline-flex items-center gap-1 group">
                                <x-badge variant="emerald">{{ $location->books()->count() }} Koleksi</x-badge>
                            </a>
                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end space-x-1.5">
                                <a href="{{ route('admin.locations.show', $location) }}" class="w-8 h-8 inline-flex items-center justify-center p-1.5 text-neutral-body hover:text-primary hover:bg-primary-light rounded transition-colors" title="Lihat Koleksi Buku di Rak Ini">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                <a href="{{ route('admin.locations.edit', $location) }}" class="w-8 h-8 inline-flex items-center justify-center p-1.5 text-neutral-body hover:text-primary hover:bg-primary-light rounded transition-colors" title="Ubah Lokasi Rak">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                    </svg>
                                </a>
                                <form action="{{ route('admin.locations.destroy', $location) }}" method="POST" class="inline" data-confirm-message="Apakah Anda yakin ingin menghapus lokasi rak '{{ $location->name }}'? Data yang terhapus tidak dapat dipulihkan.">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-8 h-8 inline-flex items-center justify-center p-1.5 text-neutral-muted hover:text-danger hover:bg-red-50 rounded transition-colors cursor-pointer" title="Hapus Lokasi Rak">
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
                            Belum ada lokasi rak terdaftar.
                        </td>
                    </tr>
                @endforelse
            </x-table>
        </div>

        <div class="mt-6 pt-4 border-t border-neutral-border">
            {{ $locations->links() }}
        </div>
    </div>
</x-app-layout>
