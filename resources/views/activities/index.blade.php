<x-app-layout>
    <x-slot name="header">
        Log Aktivitas & Riwayat Sirkulasi
    </x-slot>

    <div class="space-y-6 animate-in fade-in duration-300">
        <div class="space-y-1">
            <span class="text-xs uppercase tracking-[0.2em] font-semibold text-primary block">Buku Catatan Digital</span>
            <h2 class="font-serif text-2xl font-normal text-neutral-dark tracking-tight">Kronologi Aktivitas Sistem</h2>
            <p class="text-xs sm:text-sm text-neutral-body">Rekaman kronologis setiap pengajuan peminjaman, pengembalian, dan sirkulasi koleksi RPK PUSTAKA IMM SAINTEK MU.</p>
        </div>

        <x-card>
            <x-slot name="header">Daftar Rekam Jejak Sirkulasi</x-slot>

            <div class="space-y-3">
                @forelse($activities as $loan)
                    <div class="flex items-start space-x-4 p-4 bg-[#F8F8F7] border border-neutral-border rounded-md hover:border-primary/50 transition-all">
                        <div @class([
                            'w-9 h-9 rounded-md flex items-center justify-center shrink-0 border text-xs font-bold font-serif',
                            'bg-primary-light text-primary border-red-200' => $loan->status === 'borrowed',
                            'bg-green-50 text-success border-green-200' => $loan->status === 'returned',
                            'bg-red-50 text-danger border-red-200' => $loan->status === 'overdue',
                        ])>
                            @if($loan->status === 'borrowed')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                            @elseif($loan->status === 'returned')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            @else
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            @endif
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                                <p class="text-xs sm:text-sm font-medium text-neutral-dark">
                                    <span class="font-semibold text-primary">{{ $loan->user->name }}</span>
                                    @if($loan->status === 'borrowed')
                                        mengajukan peminjaman buku
                                    @elseif($loan->status === 'returned')
                                        telah mengembalikan buku ke rak
                                    @else
                                        tercatat melewati batas waktu pengembalian
                                    @endif
                                </p>
                                <span class="text-[10px] text-neutral-muted italic shrink-0">
                                    {{ $loan->created_at->diffForHumans() }}
                                </span>
                            </div>

                            <div class="mt-2 flex flex-wrap items-center gap-1.5">
                                @foreach($loan->loanDetails as $detail)
                                    <span class="bg-white border border-neutral-border px-2.5 py-0.5 rounded text-xs text-neutral-dark font-medium truncate max-w-[240px]">
                                        {{ $detail->book->title }}
                                    </span>
                                @endforeach
                            </div>

                            <div class="mt-2.5 text-[10px] text-neutral-muted font-semibold uppercase tracking-wider flex items-center space-x-3">
                                <span class="font-mono">ID Transaksi: #{{ $loan->id }}</span>
                                <span>•</span>
                                <span class="font-mono">{{ $loan->loan_code }}</span>
                                @if($loan->fine_amount > 0)
                                    <span>•</span>
                                    <span class="text-danger font-bold bg-red-50 px-2 py-0.5 rounded border border-red-200">
                                        Denda: Rp {{ number_format($loan->fine_amount) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center text-xs text-neutral-muted italic">
                        Belum ada riwayat aktivitas sirkulasi yang tercatat.
                    </div>
                @endforelse
            </div>

            <div class="mt-8 pt-4 border-t border-neutral-border">
                {{ $activities->links() }}
            </div>
        </x-card>
    </div>
</x-app-layout>
