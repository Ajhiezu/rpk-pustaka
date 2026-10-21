<x-app-layout>
    <x-slot name="header">
        Log Aktivitas & Riwayat Sirkulasi
    </x-slot>

    <div class="space-y-6 animate-in fade-in duration-300">
        <div class="space-y-1">
            <span class="text-xs uppercase tracking-[0.2em] font-semibold text-primary block">Buku Catatan Digital</span>
            <h2 class="font-sans text-2xl sm:text-3xl font-bold text-neutral-dark tracking-tight">Kronologi Aktivitas Sistem</h2>
            <p class="text-xs sm:text-sm text-neutral-body">Rekaman kronologis setiap pengajuan peminjaman, pengembalian, dan sirkulasi koleksi RPK PUSTAKA IMM SAINTEKMU.</p>
        </div>

        <x-card>
            <x-slot name="header">Daftar Rekam Jejak Sirkulasi</x-slot>

            <div class="space-y-3">
                @forelse($activities as $loan)
                    <div class="flex items-start space-x-4 p-4 bg-[#F8F8F7] border border-neutral-border rounded-md hover:border-primary/50 transition-all">
                        <div @class([
                            'w-9 h-9 rounded-md flex items-center justify-center shrink-0 border text-xs font-bold font-sans',
                            'bg-primary-light text-primary border-red-200' => $loan->status === 'borrowed',
                            'bg-green-50 text-success border-green-200' => $loan->status === 'returned',
                            'bg-red-50 text-danger border-red-200' => in_array($loan->status, ['overdue', 'rejected']),
                            'bg-neutral-100 text-neutral-600 border-neutral-200' => in_array($loan->status, ['cancelled', 'expired']),
                            'bg-amber-50 text-amber-700 border-amber-200' => in_array($loan->status, ['pending', 'partially_returned']),
                            'bg-blue-50 text-blue-700 border-blue-200' => $loan->status === 'approved',
                        ])>
                            @if($loan->status === 'borrowed')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                            @elseif($loan->status === 'returned')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            @elseif($loan->status === 'cancelled')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            @elseif($loan->status === 'approved')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            @elseif($loan->status === 'rejected')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                            @elseif($loan->status === 'pending')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            @else
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            @endif
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                                <p class="text-xs sm:text-sm font-medium text-neutral-dark">
                                    <span class="font-semibold text-primary">{{ $loan->user->name ?? 'Anggota' }}</span>
                                    @if($loan->status === 'borrowed')
                                        {{ $loan->isDigital() ? 'sedang membaca buku digital' : 'sedang meminjam koleksi buku fisik' }}
                                    @elseif($loan->status === 'returned')
                                        {{ $loan->isDigital() ? 'telah selesai membaca buku digital (otomatis dikembalikan)' : 'telah mengembalikan buku ke rak' }}
                                    @elseif($loan->status === 'cancelled')
                                        membatalkan pengajuan peminjaman
                                    @elseif($loan->status === 'pending')
                                        mengajukan peminjaman buku (menunggu persetujuan)
                                    @elseif($loan->status === 'approved')
                                        peminjaman disetujui (siap diambil)
                                    @elseif($loan->status === 'rejected')
                                        pengajuan peminjaman ditolak
                                    @elseif($loan->status === 'overdue')
                                        tercatat melewati batas waktu pengembalian
                                    @elseif($loan->status === 'partially_returned')
                                        mengembalikan sebagian buku
                                    @else
                                        {{ $loan->status_label }}
                                    @endif
                                </p>
                                <span class="text-[10px] text-neutral-muted italic shrink-0">
                                    {{ $loan->created_at->diffForHumans() }}
                                </span>
                            </div>

                            <div class="mt-2 flex flex-wrap items-center gap-1.5">
                                @if($loan->isDigital())
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-[#FFF9ED] text-[#B45309] border border-[#FDE68A]">
                                        Digital
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-neutral-100 text-neutral-dark border border-neutral-200">
                                        Fisik
                                    </span>
                                @endif
                                @foreach($loan->loanDetails as $detail)
                                    <span class="bg-white border border-neutral-border px-2.5 py-0.5 rounded text-xs text-neutral-dark font-medium truncate max-w-[240px]">
                                        {{ $detail->book->title ?? '-' }}
                                    </span>
                                @endforeach
                            </div>

                            <div class="mt-2.5 text-[10px] text-neutral-muted font-semibold uppercase tracking-wider flex items-center space-x-3">
                                <span class="font-mono">ID: #{{ $loan->id }}</span>
                                <span>•</span>
                                <span class="font-mono">{{ $loan->loan_code }}</span>
                                <span>•</span>
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded font-bold
                                    @if($loan->status === 'borrowed') bg-primary-light text-primary border border-red-200
                                    @elseif($loan->status === 'returned') bg-green-50 text-success border border-green-200
                                    @elseif($loan->status === 'pending') bg-amber-50 text-amber-800 border border-amber-200
                                    @elseif($loan->status === 'approved') bg-blue-50 text-blue-800 border border-blue-200
                                    @elseif($loan->status === 'cancelled') bg-neutral-100 text-neutral-600 border border-neutral-300
                                    @elseif($loan->status === 'overdue') bg-red-50 text-danger border border-red-200
                                    @elseif($loan->status === 'rejected') bg-red-50 text-danger border border-red-200
                                    @else bg-neutral-100 text-neutral-700 border border-neutral-200 @endif">
                                    {{ $loan->status_label }}
                                </span>
                                @if(!$loan->isDigital() && $loan->fine && $loan->fine->amount > 0)
                                    <span>•</span>
                                    <span class="text-danger font-bold bg-red-50 px-2 py-0.5 rounded border border-red-200">
                                        Denda: Rp {{ number_format($loan->fine->amount, 0, ',', '.') }}
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
