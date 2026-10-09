<x-app-layout>
    <x-slot name="header">
        Laporan & Rekapitulasi Sirkulasi
    </x-slot>

    <x-slot name="actions">
        <div class="flex items-center gap-2.5">
            <a href="{{ route('admin.reports.pdf', request()->all()) }}" class="btn-editorial-outline text-xs py-2 px-3.5 shadow-xs inline-flex items-center">
                <svg class="w-4 h-4 mr-1.5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                Cetak Dokumen PDF
            </a>
            <a href="{{ route('admin.reports.excel', request()->all()) }}" class="btn-editorial-outline text-xs py-2 px-3.5 shadow-xs inline-flex items-center">
                <svg class="w-4 h-4 mr-1.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Ekspor Excel
            </a>
        </div>
    </x-slot>

    <div class="space-y-8 animate-in fade-in duration-300">
        <!-- Filter Toolbar -->
        <x-card>
            <x-slot name="header">Parameter Filter Rekapitulasi</x-slot>
            <form action="{{ route('admin.reports.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div class="space-y-1.5">
                    <label class="text-[11px] font-semibold text-neutral-dark uppercase tracking-wider block px-0.5">Tanggal Awal</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" 
                        class="w-full px-3 py-2 bg-neutral-surface border border-neutral-border rounded-md text-sm text-neutral-dark focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary shadow-xs">
                </div>
                <div class="space-y-1.5">
                    <label class="text-[11px] font-semibold text-neutral-dark uppercase tracking-wider block px-0.5">Tanggal Akhir</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" 
                        class="w-full px-3 py-2 bg-neutral-surface border border-neutral-border rounded-md text-sm text-neutral-dark focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary shadow-xs">
                </div>
                <div class="space-y-1.5">
                    <label class="text-[11px] font-semibold text-neutral-dark uppercase tracking-wider block px-0.5">Status Sirkulasi</label>
                    <select name="status" 
                        class="w-full px-3 py-2 bg-neutral-surface border border-neutral-border rounded-md text-sm text-neutral-dark focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary shadow-xs">
                        <option value="">Seluruh Status</option>
                        <option value="borrowed" {{ request('status') == 'borrowed' ? 'selected' : '' }}>Sedang Dipinjam</option>
                        <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Telah Kembali</option>
                        <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Terlambat</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>
                <div class="flex space-x-2">
                    <x-button type="submit" variant="primary" class="flex-1 text-xs uppercase tracking-wider font-semibold py-2">
                        Terapkan Filter
                    </x-button>
                    <a href="{{ route('admin.reports.index') }}" class="btn-editorial-outline text-xs uppercase tracking-wider font-semibold py-2 px-3">
                        Reset
                    </a>
                </div>
            </form>
        </x-card>

        <!-- Data Display Area -->
        <div class="space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-1">
                <div>
                    <h3 class="font-bold text-lg text-neutral-dark">Hasil Rekapitulasi Data</h3>
                    <p class="text-xs text-neutral-body">Laporan arsip sirkulasi koleksi perpustakaan sesuai rentang tanggal.</p>
                </div>
                <div class="flex items-center space-x-2.5">
                    <a href="{{ route('admin.reports.pdf', request()->all()) }}" class="btn-editorial-outline text-xs py-2 px-3.5 shadow-xs">
                        <svg class="w-4 h-4 mr-1.5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        Cetak Dokumen PDF
                    </a>
                    <a href="{{ route('admin.reports.excel', request()->all()) }}" class="btn-editorial-outline text-xs py-2 px-3.5 shadow-xs">
                        <svg class="w-4 h-4 mr-1.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Ekspor Excel
                    </a>
                </div>
            </div>

            <!-- Responsive Mobile Cards (visible on mobile only) -->
            <div class="block md:hidden divide-y divide-neutral-border">
                @forelse($loans as $loan)
                    <div class="p-4 bg-white hover:bg-neutral-surface/50 transition-colors">
                        <div class="flex items-start justify-between gap-2 mb-2">
                            <div>
                                <span class="font-mono text-xs font-bold text-primary block leading-tight">{{ $loan->loan_code ?? '#'.$loan->id }}</span>
                                <span class="text-[10px] text-neutral-muted block font-mono">ID: #{{ $loan->id }}</span>
                            </div>
                            @if($loan->status === 'returned')
                                <x-badge variant="emerald">Kembali</x-badge>
                            @elseif($loan->status === 'borrowed')
                                <x-badge variant="indigo">Aktif</x-badge>
                            @elseif($loan->status === 'overdue')
                                <x-badge variant="rose">Terlambat</x-badge>
                            @else
                                <x-badge variant="slate">Batal</x-badge>
                            @endif
                        </div>

                        <div class="mb-2">
                            <span class="font-semibold text-xs text-neutral-dark block leading-tight">{{ $loan->user->name }}</span>
                            <span class="text-[11px] text-neutral-body block">{{ $loan->user->email }}</span>
                        </div>

                        <div class="text-xs text-neutral-dark mb-2.5 bg-neutral-surface p-2 rounded border border-neutral-border/60">
                            <span class="text-[10px] uppercase font-bold text-neutral-muted block tracking-wider mb-0.5">Koleksi Buku:</span>
                            <p class="line-clamp-2 text-xs text-neutral-dark font-medium">{{ $loan->loanDetails->map(fn($d) => $d->book->title)->implode(', ') }}</p>
                        </div>

                        <div class="flex items-center justify-between text-xs pt-1 border-t border-neutral-border/60">
                            <div>
                                <span class="text-[10px] text-neutral-muted block">Periode</span>
                                <span class="font-semibold text-neutral-dark text-[11px]">
                                    {{ $loan->loan_date ? \Carbon\Carbon::parse($loan->loan_date)->format('d M Y') : '-' }} - {{ $loan->due_date ? \Carbon\Carbon::parse($loan->due_date)->format('d M Y') : '-' }}
                                </span>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] text-neutral-muted block">Denda</span>
                                <span @class(['font-bold text-xs', 'text-danger' => $loan->fine_amount > 0, 'text-neutral-muted' => $loan->fine_amount == 0])>
                                    Rp {{ number_format($loan->fine_amount ?? 0, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-xs text-neutral-muted italic">
                        Tidak ada rekaman data sirkulasi yang sesuai dengan filter yang dipilih.
                    </div>
                @endforelse
            </div>

            <!-- Desktop Table (hidden on mobile) -->
            <div class="hidden md:block">
                <x-table :headers="['ID & KODE', 'PEMINJAM', 'KOLEKSI BUKU', 'PERIODE PINJAM', 'DENDA', 'STATUS']">
                    @forelse($loans as $loan)
                        <tr class="hover:bg-neutral-surface transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-mono text-xs font-bold text-primary block leading-tight">{{ $loan->loan_code ?? '#'.$loan->id }}</span>
                                <span class="text-[10px] text-neutral-muted block font-mono">ID: #{{ $loan->id }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-semibold text-xs text-neutral-dark block leading-tight">{{ $loan->user->name }}</span>
                                <span class="text-[11px] text-neutral-body block mt-0.5">{{ $loan->user->email }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-xs text-neutral-dark line-clamp-1 max-w-[280px]">
                                    {{ $loan->loanDetails->map(fn($d) => $d->book->title)->implode(', ') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-xs font-semibold text-neutral-dark block leading-tight">
                                    {{ $loan->loan_date ? \Carbon\Carbon::parse($loan->loan_date)->format('d M Y') : '-' }}
                                </span>
                                <span class="text-[11px] text-neutral-muted block mt-0.5">
                                    s.d. {{ $loan->due_date ? \Carbon\Carbon::parse($loan->due_date)->format('d M Y') : '-' }}
                                </span>
                            </td>
                            <td @class(['px-6 py-4 text-xs font-bold whitespace-nowrap', 'text-danger' => $loan->fine_amount > 0, 'text-neutral-muted' => $loan->fine_amount == 0])>
                                Rp {{ number_format($loan->fine_amount ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($loan->status === 'returned')
                                    <x-badge variant="emerald">Kembali</x-badge>
                                @elseif($loan->status === 'borrowed')
                                    <x-badge variant="indigo">Aktif</x-badge>
                                @elseif($loan->status === 'overdue')
                                    <x-badge variant="rose">Terlambat</x-badge>
                                @else
                                    <x-badge variant="slate">Batal</x-badge>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-xs text-neutral-muted italic">
                                Tidak ada rekaman data sirkulasi yang sesuai dengan filter yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </x-table>
            </div>

            <div class="pt-4">
                {{ $loans->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
