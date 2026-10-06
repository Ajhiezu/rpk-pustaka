<x-app-layout>
    <x-slot name="header">
        Kelola Artikel & Publikasi Ilmiah, RPK PUSTAKA IMM SAINTEKMU
    </x-slot>

    <x-slot name="actions">
        <a href="{{ route('admin.articles.create') }}" class="btn-editorial text-xs py-2 px-4 shadow-xs">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tulis Artikel Baru
        </a>
    </x-slot>

    <div class="space-y-6 animate-in fade-in duration-300">
        <!-- Toolbar & Filter -->
        <div class="bg-white p-4 rounded-lg border border-neutral-border shadow-xs">
            <form action="{{ route('admin.articles.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1 relative">
                    <svg class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-neutral-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari artikel berdasarkan judul..." 
                        class="w-full pl-10 pr-4 py-2 bg-neutral-surface border border-neutral-border rounded-md text-sm text-neutral-dark placeholder:text-neutral-muted focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                </div>
                <select name="status" onchange="if (window.performLiveSearch) { window.performLiveSearch(this.form); } else { this.form.submit(); }" class="px-3 py-2 bg-neutral-surface border border-neutral-border rounded-md text-xs font-semibold text-neutral-dark focus:outline-none focus:ring-2 focus:ring-primary/20">
                    <option value="">Semua Status</option>
                    <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                </select>
                <x-button type="submit" variant="primary" class="px-5 text-xs uppercase tracking-wider font-semibold">Cari</x-button>
            </form>
        </div>

        <!-- Articles Table -->
        <x-table :headers="['Artikel & Penulis', 'Status', 'Tanggal Terbit', 'Aksi']">
            @forelse($articles as $article)
                <tr class="group transition-colors hover:bg-neutral-surface">
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-3.5">
                            @if($article->cover_image)
                                <img src="{{ asset('storage/'.$article->cover_image) }}" class="w-14 h-10 object-cover rounded border border-neutral-border shrink-0">
                            @else
                                <div class="w-14 h-10 bg-neutral-surface rounded flex items-center justify-center text-neutral-muted border border-neutral-border shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                                </div>
                            @endif
                            <div class="min-w-0">
                                <a href="{{ route('admin.articles.edit', $article) }}" class="font-serif text-sm font-semibold text-neutral-dark hover:text-primary transition-colors block line-clamp-1">
                                    {{ $article->title }}
                                </a>
                                <p class="text-[11px] text-neutral-muted mt-0.5">Penulis: {{ $article->user->name ?? 'Admin' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($article->status === 'published')
                            <x-badge variant="emerald">Published</x-badge>
                        @else
                            <x-badge variant="slate">Draft</x-badge>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-xs text-neutral-dark">
                        {{ $article->published_at ? $article->published_at->format('d M Y') : '—' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center space-x-2">
                            <form action="{{ route('admin.articles.toggle', $article) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-2.5 py-1 text-[11px] font-semibold rounded border transition-colors {{ $article->status === 'published' ? 'border-amber-300 text-[#B45309] hover:bg-amber-50' : 'border-green-300 text-success hover:bg-green-50' }}">
                                    {{ $article->status === 'published' ? 'Jadikan Draft' : 'Terbitkan' }}
                                </button>
                            </form>
                            <a href="{{ route('admin.articles.edit', $article) }}" class="p-1.5 text-neutral-body hover:text-primary hover:bg-primary-light rounded transition-colors" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </a>
                            <form action="{{ route('admin.articles.destroy', $article) }}" method="POST" class="inline" data-confirm-message="Apakah Anda yakin ingin menghapus artikel '{{ $article->title }}'?">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-neutral-muted hover:text-danger hover:bg-red-50 rounded transition-colors cursor-pointer" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-xs text-neutral-muted italic">
                        Belum ada artikel yang dibuat.
                    </td>
                </tr>
            @endforelse
        </x-table>

        <div class="pt-4">
            {{ $articles->links() }}
        </div>
    </div>
</x-app-layout>
