<x-app-layout>
    <x-slot name="header">
        Bukti Peminjaman — {{ $loan->loan_code }}
    </x-slot>

    <div class="max-w-3xl mx-auto space-y-6 animate-in fade-in duration-300 print:max-w-none print:m-0">
        <!-- Navigation Back -->
        <div class="flex items-center justify-between print:hidden">
            <a href="{{ route('anggota.loans.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-neutral-body hover:text-primary transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke Daftar Peminjaman
            </a>

            @if($loan->isPending() || $loan->isApproved())
                <button onclick="window.print()" class="btn-editorial-outline text-xs py-1.5 px-3 flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Cetak Bukti Peminjaman
                </button>
            @endif
        </div>

        <!-- Main Ticket/Receipt Card -->
        <div class="bg-white border-2 border-neutral-border rounded-xl shadow-sm overflow-hidden print:border print:shadow-none">
            <!-- Receipt Header -->
            <div class="bg-neutral-surface px-6 py-5 border-b border-neutral-border flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-lg bg-primary-light border border-red-200 text-primary flex items-center justify-center shrink-0">
                        <img src="{{ asset('images/logo-rpk.png') }}" alt="Logo RPK" class="w-8 h-8 object-contain">
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-neutral-muted uppercase tracking-widest block">RPK PUSTAKA IMM SAINTEK MU</span>
                        <h2 class="font-sans text-xl font-extrabold text-neutral-dark tracking-tight">BUKTI RESERVASI & PEMINJAMAN</h2>
                    </div>
                </div>

                <div class="sm:text-right flex flex-col sm:items-end gap-1">
                    <span class="text-[10px] font-bold text-neutral-muted uppercase tracking-wider block">KODE TRANSAKSI</span>
                    <span class="font-mono text-base font-extrabold text-primary block">{{ $loan->loan_code }}</span>
                    <span class="inline-block px-2.5 py-0.5 text-xs font-bold rounded-full
                        @if($loan->isPending()) bg-amber-100 text-amber-800 border border-amber-300
                        @elseif($loan->isApproved()) bg-blue-100 text-blue-800 border border-blue-300
                        @elseif($loan->isBorrowed()) bg-emerald-100 text-emerald-800 border border-emerald-300
                        @elseif($loan->isReturned()) bg-slate-100 text-slate-800 border border-slate-300
                        @elseif($loan->isCancelled() || $loan->isRejected() || $loan->isExpiredState()) bg-red-100 text-red-800 border border-red-300
                        @else bg-neutral-100 text-neutral-800 border border-neutral-300 @endif">
                        {{ $loan->status_label }}
                    </span>
                </div>
            </div>

            <!-- Receipt Status Banner -->
            <div class="px-6 py-4 border-b border-neutral-border
                @if($loan->isPending()) bg-amber-50 text-amber-900 border-amber-200
                @elseif($loan->isApproved()) bg-blue-50 text-blue-900 border-blue-200
                @elseif($loan->isBorrowed()) bg-emerald-50 text-emerald-900 border-emerald-200
                @elseif($loan->isReturned()) bg-slate-50 text-slate-800 border-slate-200
                @elseif($loan->isCancelled() || $loan->isRejected() || $loan->isExpiredState()) bg-red-50 text-red-900 border-red-200
                @else bg-neutral-50 text-neutral-800 @endif">

                <div class="flex items-start gap-3">
                    @if($loan->isPending())
                        <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div>
                            <span class="font-bold text-sm block">Status: {{ $loan->status_label }}</span>
                            <p class="text-xs mt-0.5">🔒 Buku fisik telah di-reserve secara eksklusif untuk Anda. Silakan tunggu persetujuan Admin atau langsung tunjukkan bukti ini di meja sirkulasi perpustakaan.</p>
                        </div>
                    @elseif($loan->isApproved())
                        <svg class="w-5 h-5 text-blue-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div>
                            <span class="font-bold text-sm block">Status: {{ $loan->status_label }}</span>
                            <p class="text-xs mt-0.5">Silakan datang ke perpustakaan dan tunjukkan bukti peminjaman ini kepada Admin untuk pengambilan buku fisik.</p>
                        </div>
                    @elseif($loan->isBorrowed())
                        <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <div>
                            <span class="font-bold text-sm block">Status: {{ $loan->status_label }}</span>
                            <p class="text-xs mt-0.5">Buku fisik telah diserahkan dan saat ini sedang dalam masa peminjaman aktif Anda.</p>
                        </div>
                    @elseif($loan->isCancelled())
                        <svg class="w-5 h-5 text-red-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        <div>
                            <span class="font-bold text-sm block">Status: {{ $loan->status_label }}</span>
                            <p class="text-xs mt-0.5">Reservasi telah dibatalkan. Stok buku telah dilepas kembali ke perpustakaan.</p>
                        </div>
                    @elseif($loan->isRejected())
                        <svg class="w-5 h-5 text-red-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div>
                            <span class="font-bold text-sm block">Status: {{ $loan->status_label }}</span>
                            <p class="text-xs mt-0.5">Reservasi ditolak oleh Petugas. @if($loan->rejection_reason) Alasan: <strong>{{ $loan->rejection_reason }}</strong> @endif</p>
                        </div>
                    @elseif($loan->isExpiredState())
                        <svg class="w-5 h-5 text-red-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div>
                            <span class="font-bold text-sm block">Status: {{ $loan->status_label }}</span>
                            <p class="text-xs mt-0.5">Batas waktu pengambilan telah terlewati. Reservasi telah kedaluwarsa dan stok buku dilepas kembali.</p>
                        </div>
                    @else
                        <div>
                            <span class="font-bold text-sm block">Status: {{ $loan->status_label }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Detail Information Grid -->
            <div class="p-6 space-y-6">
                <!-- Member & Deadline Box -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-neutral-surface p-4 rounded-lg border border-neutral-border">
                    <div class="space-y-1">
                        <span class="text-[10px] font-bold text-neutral-muted uppercase tracking-wider block">PEMINJAM / ANGGOTA</span>
                        <p class="font-sans text-base font-bold text-neutral-dark">{{ $loan->user->name }}</p>
                        <p class="text-xs text-neutral-body">{{ $loan->user->email }} • {{ ucfirst($loan->user->role) }}</p>
                    </div>

                    <div class="space-y-1 md:text-right">
                        @if($loan->pickup_deadline && ($loan->isPending() || $loan->isApproved()))
                            <span class="text-[10px] font-bold text-red-600 uppercase tracking-wider block">BATAS MAKSIMAL PENGAMBILAN</span>
                            <p class="font-sans text-base font-bold text-primary">{{ $loan->pickup_deadline->format('d F Y, H:i') }} WIB</p>
                            
                            @if($loan->isPending())
                                @php
                                    $diffMinutes = now()->diffInMinutes($loan->pickup_deadline, false);
                                    $hoursLeft = floor(max(0, $diffMinutes) / 60);
                                    $minsLeft = max(0, $diffMinutes) % 60;
                                @endphp
                                @if($diffMinutes > 0)
                                    <span class="inline-block text-[11px] font-semibold text-amber-800 bg-amber-100 px-2 py-0.5 rounded mt-1">
                                        ⏱️ Sisa waktu pengambilan: {{ $hoursLeft }} jam {{ $minsLeft }} menit
                                    </span>
                                @else
                                    <span class="inline-block text-[11px] font-bold text-red-700 bg-red-100 px-2 py-0.5 rounded mt-1">
                                        ⚠️ Menunggu proses kedaluwarsa
                                    </span>
                                @endif
                            @endif
                        @else
                            <span class="text-[10px] font-bold text-neutral-muted uppercase tracking-wider block">TANGGAL PENGAJUAN</span>
                            <p class="font-sans text-base font-bold text-neutral-dark">{{ $loan->created_at->format('d F Y, H:i') }} WIB</p>
                            <p class="text-xs text-neutral-body">Jatuh Tempo: {{ $loan->due_date->format('d F Y') }}</p>
                        @endif
                    </div>
                </div>

                <!-- Book Item Details -->
                <div>
                    <h3 class="text-xs uppercase font-bold text-neutral-muted tracking-wider mb-3">KOLEKSI BUKU YANG DI-RESERVE</h3>

                    <div class="border border-neutral-border rounded-lg overflow-hidden divide-y divide-neutral-border">
                        @foreach($loan->loanDetails as $detail)
                            @if($detail->book)
                                <div class="p-4 flex items-start space-x-4 bg-white">
                                    @if($detail->book->cover_image)
                                        <img src="{{ asset('storage/' . $detail->book->cover_image) }}" alt="{{ $detail->book->title }}" class="w-14 h-20 object-cover rounded border border-neutral-border shrink-0">
                                    @else
                                        <div class="w-14 h-20 rounded bg-primary-light border border-red-200 text-primary flex items-center justify-center font-bold text-xs shrink-0">
                                            RPK
                                        </div>
                                    @endif

                                    <div class="flex-1 min-w-0">
                                        <span class="inline-block px-2 py-0.5 text-[9px] font-bold uppercase rounded bg-neutral-100 text-neutral-dark mb-1">
                                            Kode Book: RPK-B{{ str_pad($detail->book->id, 4, '0', STR_PAD_LEFT) }}
                                        </span>
                                        <h4 class="font-sans text-base font-bold text-neutral-dark leading-snug">{{ $detail->book->title }}</h4>
                                        <p class="text-xs text-neutral-body mt-0.5">Penulis: {{ $detail->book->author }} • Penerbit: {{ $detail->book->publisher ?? '-' }}</p>

                                        <div class="mt-2 pt-2 border-t border-dashed border-neutral-border flex flex-wrap items-center text-xs text-neutral-muted gap-4">
                                            <span><strong>Lokasi Rak:</strong> {{ $detail->book->location->name ?? 'Perpustakaan Utama' }}</span>
                                            <span><strong>ISBN:</strong> {{ $detail->book->isbn ?? '-' }}</span>
                                            <span><strong>Jenis Koleksi:</strong> {{ ucfirst($detail->book->collection_type) }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>

                <!-- Pickup Instructions -->
                <div class="bg-neutral-surface p-4 rounded-lg border border-neutral-border space-y-2 text-xs text-neutral-body">
                    <h4 class="font-bold text-neutral-dark uppercase tracking-wider text-[11px]">PETUNJUK PENGAMBILAN BUKU:</h4>
                    <ol class="list-decimal list-inside space-y-1 pl-1">
                        <li>Tunjukkan bukti layar (screenshot) atau cetakan fisik halaman ini kepada Petugas Perpustakaan.</li>
                        <li>Petugas akan mengonfirmasi Kode Transaksi: <strong class="font-mono text-primary">{{ $loan->loan_code }}</strong>.</li>
                        <li>Setelah diverifikasi, fisik buku akan diserahkan dan status peminjaman Anda otomatis menjadi <strong>Aktif (Borrowed)</strong>.</li>
                        <li>Pastikan Anda mengembalikan buku tepat waktu sebelum tanggal <strong>{{ $loan->due_date->format('d F Y') }}</strong>.</li>
                    </ol>
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="bg-neutral-surface px-6 py-4 border-t border-neutral-border flex flex-col sm:flex-row sm:items-center justify-between gap-3 print:hidden">
                <div class="text-xs text-neutral-muted">
                    Dicetak / Diakses pada: {{ now()->format('d M Y H:i:s') }} WIB
                </div>

                <div class="flex items-center gap-3">
                    @if($loan->isPending())
                        <form action="{{ route('anggota.loans.cancel', $loan) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan reservasi ini? Stok buku akan dilepas untuk anggota lain.');">
                            @csrf
                            <button type="submit" class="px-4 py-2 text-xs font-semibold text-danger bg-red-50 hover:bg-red-100 border border-red-200 rounded transition-colors">
                                Batalkan Reservasi
                            </button>
                        </form>
                    @endif

                    @if($loan->isPending() || $loan->isApproved())
                        <button onclick="window.print()" class="btn-editorial text-xs py-2 px-4">
                            Cetak / Simpan Bukti PDF
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
