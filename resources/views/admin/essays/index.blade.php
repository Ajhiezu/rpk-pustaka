<x-app-layout>
    <x-slot name="header">
        Kurasi & Review Esai Anggota — RPK PUSTAKA IMM SAINTEK MU
    </x-slot>

    <div class="space-y-6 animate-in fade-in duration-300">
        <!-- Toolbar & Filter -->
        <div class="bg-white p-4 rounded-lg border border-neutral-border shadow-xs">
            <form action="{{ route('admin.essays.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1 relative">
                    <svg class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-neutral-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari esai berdasarkan judul atau nama anggota..." 
                        class="w-full pl-10 pr-4 py-2 bg-neutral-surface border border-neutral-border rounded-md text-sm text-neutral-dark placeholder:text-neutral-muted focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                </div>
                <select name="status" onchange="if (window.performLiveSearch) { window.performLiveSearch(this.form); } else { this.form.submit(); }" class="px-3 py-2 bg-neutral-surface border border-neutral-border rounded-md text-xs font-semibold text-neutral-dark focus:outline-none focus:ring-2 focus:ring-primary/20">
                    <option value="">Semua Status Kurasi</option>
                    <option value="submitted" {{ request('status') === 'submitted' ? 'selected' : '' }}>Menunggu Review</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui</option>
                    <option value="revision" {{ request('status') === 'revision' ? 'selected' : '' }}>Perlu Revisi</option>
                    <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Dipublikasikan</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                </select>
                <x-button type="submit" variant="primary" class="px-5 text-xs uppercase tracking-wider font-semibold">Cari</x-button>
            </form>
        </div>

        <!-- Essays Table -->
        <x-table :headers="['Nama Anggota', 'Judul Karya Tulis', 'Format Pengajuan', 'Tanggal Diajukan', 'Status Kurasi', 'Aksi']">
            @forelse($essays as $essay)
                <tr class="group transition-colors hover:bg-neutral-surface">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center space-x-2.5">
                            <div class="w-8 h-8 rounded-full bg-neutral-surface border border-neutral-border flex items-center justify-center font-bold text-xs text-neutral-dark">
                                {{ substr($essay->user->name ?? 'A', 0, 1) }}
                            </div>
                            <div>
                                <span class="font-semibold text-xs text-neutral-dark block">{{ $essay->user->name ?? 'Anggota' }}</span>
                                <span class="text-[10px] text-neutral-muted">{{ $essay->user->email ?? '' }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <a href="{{ route('admin.essays.show', $essay) }}" class="font-serif text-sm font-semibold text-neutral-dark hover:text-primary transition-colors block line-clamp-1">
                            {{ $essay->title }}
                        </a>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($essay->isOnline())
                            <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-primary bg-primary-light px-2.5 py-0.5 rounded border border-red-200">
                                Tulis Online
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-[#B45309] bg-[#FFF9ED] px-2.5 py-0.5 rounded border border-[#FDE68A]">
                                Berkas {{ strtoupper($essay->file_type ?? 'Doc') }}
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-xs text-neutral-muted">
                        {{ $essay->created_at->format('d M Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($essay->status === 'submitted')
                            <x-badge variant="indigo">Menunggu Review</x-badge>
                        @elseif($essay->status === 'approved')
                            <x-badge variant="emerald">Disetujui</x-badge>
                        @elseif($essay->status === 'published')
                            <x-badge variant="primary">Published</x-badge>
                        @elseif($essay->status === 'revision')
                            <x-badge variant="amber">Perlu Revisi</x-badge>
                        @elseif($essay->status === 'rejected')
                            <x-badge variant="rose">Ditolak</x-badge>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <a href="{{ route('admin.essays.show', $essay) }}" class="btn-editorial-outline text-xs py-1.5 px-3 uppercase tracking-wider font-semibold inline-flex items-center gap-1">
                            Review Esai &rarr;
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-xs text-neutral-muted italic">
                        Belum ada pengajuan tulisan atau esai dari anggota.
                    </td>
                </tr>
            @endforelse
        </x-table>

        <div class="pt-4">
            {{ $essays->links() }}
        </div>
    </div>
</x-app-layout>
