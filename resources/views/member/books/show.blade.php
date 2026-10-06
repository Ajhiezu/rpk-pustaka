<x-app-layout>
    <x-slot name="header">
        Lembar Publikasi Koleksi, RPK PUSTAKA IMM SAINTEKMU
    </x-slot>

    @php
        $digitalDays = (int) \App\Models\Setting::get('digital_loan_duration_days', 7);
        $physicalDays = (int) \App\Models\Setting::get('physical_loan_duration_days', 14);
    @endphp

    <div class="max-w-6xl mx-auto space-y-10 animate-in fade-in duration-300">
        
        <!-- Breadcrumb / Back Link -->
        <div>
            <a href="{{ Auth::check() ? route('anggota.books.index') : url('/') }}" 
               class="inline-flex items-center text-xs font-semibold uppercase tracking-wider text-neutral-muted hover:text-primary transition-colors">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                {{ Auth::check() ? 'Kembali ke Katalog Pustaka' : 'Kembali ke Beranda Pustaka' }}
            </a>
        </div>

        <!-- Main Publication Grid: [BOOK COVER & AVAILABILITY] [BOOK DETAILS & LOAN ACTION] -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
            
            <!-- Left Column: Book Cover & Shelf Metadata -->
            <div class="lg:col-span-5 space-y-6">
                <!-- Cover Card -->
                <div class="bg-white p-4 sm:p-6 rounded-lg border border-neutral-border shadow-xs">
                    <div class="aspect-[3/4.2] bg-[#F8F8F7] rounded overflow-hidden relative border border-neutral-border shadow-xs">
                        @if($book->cover_url)
                            <img id="detail-cover-img" src="{{ $book->cover_url }}" alt="{{ $book->title }}" class="w-full h-full object-cover">
                        @else
                            <img id="detail-cover-img" src="" alt="{{ $book->title }}" class="w-full h-full object-cover hidden">
                            <div id="detail-cover-placeholder" class="w-full h-full flex flex-col items-center justify-center p-8 text-center bg-[#F8F8F7]">
                                <div class="w-16 h-16 rounded-full bg-white flex items-center justify-center text-primary mb-4 shadow-xs border border-neutral-border">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                </div>
                                <span class="font-sans text-base font-semibold text-neutral-dark">{{ $book->title }}</span>
                                <span class="text-xs text-neutral-muted mt-1">{{ $book->author }}</span>
                            </div>

                            @if($book->hasDigital())
                                <script>
                                    (async function autoRenderPdfCover() {
                                        let attempts = 0;
                                        while (typeof window.renderPdfFirstPage !== 'function' && attempts < 50) {
                                            await new Promise(r => setTimeout(r, 100));
                                            attempts++;
                                        }

                                        if (typeof window.renderPdfFirstPage !== 'function') return;

                                        try {
                                            const pdfUrl = '{{ route("member.books.stream", $book) }}';
                                            const resp = await fetch(pdfUrl);
                                            if (!resp.ok) return;

                                            const blob = await resp.blob();
                                            const file = new File([blob], 'naskah.pdf', { type: 'application/pdf' });
                                            const res = await window.renderPdfFirstPage(file, 15000);

                                            if (res.success && (res.blob || res.imageBase64)) {
                                                const imgEl = document.getElementById('detail-cover-img');
                                                const placeholderEl = document.getElementById('detail-cover-placeholder');

                                                if (imgEl) {
                                                    imgEl.src = res.imageBase64;
                                                    imgEl.classList.remove('hidden');
                                                }
                                                if (placeholderEl) {
                                                    placeholderEl.classList.add('hidden');
                                                    placeholderEl.classList.remove('flex');
                                                }

                                                const formData = new FormData();
                                                formData.append('_token', '{{ csrf_token() }}');
                                                if (res.blob) {
                                                    formData.append('cover_image', res.blob, 'cover.webp');
                                                } else {
                                                    formData.append('cover_base64', res.imageBase64);
                                                }

                                                await fetch('{{ route("admin.books.auto-cover", $book) }}', {
                                                    method: 'POST',
                                                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                                                    body: formData
                                                });
                                            }
                                        } catch (err) {
                                            console.warn('Auto cover render error:', err);
                                        }
                                    })();
                                </script>
                            @endif
                        @endif
                        
                        <!-- Primary Format Pill Badge -->
                        <div class="absolute top-4 right-4 flex flex-col items-end gap-1.5">
                            @if($book->collection_type === 'fisik_digital')
                                <span class="px-2.5 py-1 bg-primary text-white text-[10px] font-bold uppercase tracking-wider rounded shadow-xs">
                                    Fisik & Digital
                                </span>
                            @elseif($book->collection_type === 'digital')
                                <span class="px-2.5 py-1 bg-[#FFF9ED] text-[#B45309] text-[10px] font-bold uppercase tracking-wider rounded border border-[#FDE68A] shadow-xs">
                                    Digital
                                </span>
                            @else
                                <span class="px-2.5 py-1 bg-[#EDF7ED] text-success text-[10px] font-bold uppercase tracking-wider rounded border border-[#C8E6C9] shadow-xs">
                                    Fisik
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Format Availability Status Box -->
                <div class="bg-white p-5 rounded-lg border border-neutral-border shadow-xs space-y-3">
                    <span class="text-[10px] font-bold text-neutral-muted uppercase tracking-[0.2em] block">Ketersediaan Koleksi</span>
                    
                    <div class="space-y-2.5 text-xs">
                        @if($book->collection_type === 'digital')
                            <!-- Digital Only -->
                            <div class="p-3 bg-primary-light/60 rounded border border-red-200 flex items-center space-x-2.5 text-neutral-dark">
                                <svg class="w-4 h-4 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span class="font-semibold text-primary">Tersedia untuk peminjaman digital</span>
                            </div>
                        @elseif($book->collection_type === 'fisik')
                            <!-- Fisik Only -->
                            <div class="p-3 rounded border flex items-center space-x-2.5 {{ $book->available_stock > 0 ? 'bg-[#EDF7ED]/60 border-green-200 text-[#2E7D32]' : 'bg-neutral-surface border-neutral-border text-neutral-muted' }}">
                                @if($book->available_stock > 0)
                                    <svg class="w-4 h-4 text-success shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    <span class="font-semibold">Tersedia {{ $book->available_stock }} eksemplar</span>
                                @else
                                    <svg class="w-4 h-4 text-neutral-muted shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    <span>Stok fisik sedang habis dipinjam</span>
                                @endif
                            </div>
                        @else
                            <!-- Fisik & Digital -->
                            <div class="p-3 bg-primary-light/60 rounded border border-red-200 flex items-center space-x-2.5 text-neutral-dark">
                                <svg class="w-4 h-4 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span class="font-semibold text-primary">Digital tersedia</span>
                            </div>
                            <div class="p-3 rounded border flex items-center space-x-2.5 {{ $book->available_stock > 0 ? 'bg-[#EDF7ED]/60 border-green-200 text-[#2E7D32]' : 'bg-neutral-surface border-neutral-border text-neutral-muted' }}">
                                @if($book->available_stock > 0)
                                    <svg class="w-4 h-4 text-success shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    <span class="font-semibold">{{ $book->available_stock }} eksemplar fisik tersedia</span>
                                @else
                                    <svg class="w-4 h-4 text-neutral-muted shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    <span>Eksemplar fisik sedang habis</span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Shelf Location (Physical Only) -->
                @if($book->location && $book->collection_type !== 'digital')
                    <div class="bg-white p-4 rounded-lg border border-neutral-border shadow-xs">
                        <span class="text-[10px] font-bold text-neutral-muted uppercase tracking-wider block mb-1">Lokasi Rak Koleksi Fisik</span>
                        <p class="font-sans text-base font-bold text-neutral-dark">{{ $book->location->name }}</p>
                        <p class="text-[10px] text-neutral-muted mt-0.5">{{ $book->location->description ?? 'Area Peminjaman Koleksi Fisik' }}</p>
                    </div>
                @endif
            </div>

            <!-- Right Column: Rich Editorial Details & Action -->
            <div class="lg:col-span-7 space-y-8 bg-white p-8 sm:p-10 rounded-lg border border-neutral-border shadow-xs">
                
                <!-- Title & Meta Header -->
                <div class="space-y-3 pb-6 border-b border-neutral-border">
                    <div class="flex flex-wrap items-center gap-2.5">
                        <span class="px-2.5 py-0.5 bg-primary-light text-primary text-[11px] font-bold uppercase tracking-wider rounded border border-red-200">
                            {{ $book->category->name ?? 'Umum' }}
                        </span>
                        <span class="font-mono text-xs font-semibold px-2 py-0.5 bg-neutral-surface border border-neutral-border rounded text-neutral-dark">
                            Kode: {{ $book->book_code }}
                        </span>
                        <span class="text-xs text-neutral-muted">Tahun: {{ $book->year ?? '-' }}</span>
                    </div>

                    <h1 class="font-sans text-2xl sm:text-3xl lg:text-4xl font-bold text-neutral-dark tracking-tight leading-tight">
                        {{ $book->title }}
                    </h1>

                    <div class="flex flex-wrap items-center gap-4 text-xs text-neutral-body pt-1">
                        <div class="flex items-center space-x-1.5">
                            <span class="text-neutral-muted">Penulis:</span>
                            <span class="font-semibold text-neutral-dark">{{ $book->author }}</span>
                        </div>
                        <span class="text-neutral-muted">•</span>
                        <div class="flex items-center space-x-1.5">
                            <span class="text-neutral-muted">ISBN:</span>
                            <span class="font-mono font-medium text-neutral-dark">{{ $book->isbn ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Synopsis / Description -->
                <div class="space-y-3">
                    <h3 class="text-xs font-bold uppercase tracking-[0.2em] text-neutral-muted">Sinopsis & Ringkasan Koleksi</h3>
                    <div class="prose max-w-none text-sm text-neutral-body leading-relaxed font-normal">
                        <p>
                            {{ $book->description ?? 'Deskripsi kuratorial belum tersedia untuk koleksi naskah ini. Silakan kunjungi meja layanan sirkulasi RPK PUSTAKA IMM SAINTEKMU untuk memeriksa koleksi naskah secara langsung.' }}
                        </p>
                    </div>
                </div>

                <!-- Metadata Specification Table -->
                <div class="space-y-3 pt-6 border-t border-neutral-border">
                    <h3 class="text-xs font-bold uppercase tracking-[0.2em] text-neutral-muted">Informasi Bibliografi & Publikasi</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div class="p-3 bg-[#F8F8F7] rounded border border-neutral-border">
                            <span class="text-[10px] uppercase tracking-wider text-neutral-muted block font-semibold">Penerbit Resmi</span>
                            <span class="font-semibold text-neutral-dark block mt-0.5">{{ $book->publisher ?? '-' }}</span>
                        </div>
                        <div class="p-3 bg-[#F8F8F7] rounded border border-neutral-border">
                            <span class="text-[10px] uppercase tracking-wider text-neutral-muted block font-semibold">Format Media</span>
                            <span class="font-semibold text-neutral-dark block mt-0.5">{{ $book->format_label }}</span>
                        </div>
                        <div class="p-3 bg-[#F8F8F7] rounded border border-neutral-border">
                            <span class="text-[10px] uppercase tracking-wider text-neutral-muted block font-semibold">Bahasa Naskah</span>
                            <span class="font-semibold text-neutral-dark block mt-0.5">{{ $book->language ?? 'Indonesia' }}</span>
                        </div>
                        <div class="p-3 bg-[#F8F8F7] rounded border border-neutral-border">
                            <span class="text-[10px] uppercase tracking-wider text-neutral-muted block font-semibold">Jumlah Halaman</span>
                            <span class="font-semibold text-neutral-dark block mt-0.5">{{ $book->page_count ? $book->page_count . ' Halaman' : '-' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Circulation / Borrowing Action Area -->
                <div class="pt-6 border-t border-neutral-border space-y-6">
                    <h3 class="text-xs font-bold uppercase tracking-[0.2em] text-neutral-muted">Layanan Sirkulasi & Peminjaman</h3>

                    @auth
                        @if(Auth::user()->isAnggota())
                            
                            <!-- 1. SECTION: PEMINJAMAN DIGITAL -->
                            @if($book->hasDigital())
                                <div class="p-5 rounded-lg border border-neutral-border bg-white shadow-xs space-y-4">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-2.5">
                                            <div class="w-8 h-8 rounded bg-primary-light text-primary flex items-center justify-center font-bold">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                            </div>
                                            <div>
                                                <h4 class="font-sans text-base font-bold text-neutral-dark">Peminjaman Digital (E-Book)</h4>
                                                <p class="text-xs text-neutral-muted">Pinjam hak akses membaca naskah digital langsung di website selama {{ $digitalDays }} hari.</p>
                                            </div>
                                        </div>
                                    </div>

                                    @if($activeDigitalLoan)
                                        <!-- User has active digital loan: direct reading action -->
                                        <div class="p-4 bg-primary-light rounded border border-red-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                            <div class="space-y-0.5">
                                                <div class="flex items-center gap-2">
                                                    <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                                                    <span class="text-xs font-bold text-primary uppercase tracking-wider">Peminjaman Digital Aktif</span>
                                                </div>
                                                <p class="text-xs text-neutral-dark">
                                                    Akses berlaku hingga <strong>{{ \Carbon\Carbon::parse($activeDigitalLoan->due_date)->translatedFormat('d F Y') }}</strong>
                                                </p>
                                            </div>
                                            <a href="{{ route('anggota.books.reader', $book) }}" class="btn-editorial py-2.5 px-6 text-xs uppercase tracking-wider inline-flex items-center justify-center gap-1.5 shadow-xs">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                Baca Buku Sekarang
                                            </a>
                                        </div>
                                    @else
                                        <!-- Digital loan available to borrow -->
                                        <form action="{{ route('anggota.loans.store') }}" method="POST" data-confirm-loan="true" data-book-title="{{ $book->title }}" data-loan-type="digital">
                                            @csrf
                                            <input type="hidden" name="book_id" value="{{ $book->id }}">
                                            <input type="hidden" name="loan_type" value="digital">
                                            <x-button type="submit" variant="primary" class="w-full py-3 text-xs uppercase tracking-wider font-semibold">
                                                Pinjam Digital & Baca Sekarang ({{ $digitalDays }} Hari)
                                            </x-button>
                                        </form>
                                    @endif
                                </div>
                            @endif

                            <!-- 2. SECTION: PEMINJAMAN FISIK -->
                            @if($book->hasPhysical())
                                <div class="p-5 rounded-lg border border-neutral-border bg-white shadow-xs space-y-4">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-2.5">
                                            <div class="w-8 h-8 rounded bg-neutral-surface text-neutral-dark flex items-center justify-center font-bold border border-neutral-border">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path></svg>
                                            </div>
                                            <div>
                                                <h4 class="font-sans text-base font-bold text-neutral-dark">Peminjaman Buku Fisik</h4>
                                                <p class="text-xs text-neutral-muted">Pinjam eksemplar fisik dari rak dan ambil di meja sirkulasi perpustakaan.</p>
                                            </div>
                                        </div>
                                    </div>

                                    @if($activePhysicalLoan)
                                        <!-- Physical loan currently active -->
                                        <div class="p-4 bg-[#EDF7ED] rounded border border-[#C8E6C9] flex items-center justify-between text-xs text-[#2E7D32]">
                                            <div>
                                                <span class="font-bold uppercase tracking-wider block">Buku Fisik Sedang Dipinjam</span>
                                                <span>Batas jatuh tempo: <strong>{{ \Carbon\Carbon::parse($activePhysicalLoan->due_date)->translatedFormat('d F Y') }}</strong></span>
                                            </div>
                                            <a href="{{ route('anggota.loans.index') }}" class="underline font-semibold">Lihat Riwayat</a>
                                        </div>
                                    @elseif($book->available_stock > 0)
                                        <!-- Physical stock available to borrow -->
                                        <form action="{{ route('anggota.loans.store') }}" method="POST" class="space-y-3.5" data-confirm-loan="true" data-book-title="{{ $book->title }}" data-loan-type="physical">
                                            @csrf
                                            <input type="hidden" name="book_id" value="{{ $book->id }}">
                                            <input type="hidden" name="loan_type" value="physical">
                                            
                                            <div class="space-y-1">
                                                <label class="block text-[11px] font-semibold text-neutral-dark uppercase tracking-wider">
                                                    Rencana Tanggal Pengembalian Fisik
                                                </label>
                                                <input type="date" name="due_date" required 
                                                    min="{{ now()->addDay()->toDateString() }}" 
                                                    max="{{ now()->addDays($physicalDays)->toDateString() }}"
                                                    value="{{ now()->addDays($physicalDays)->toDateString() }}"
                                                    class="w-full px-3.5 py-2 bg-neutral-surface border border-neutral-border rounded text-xs font-medium text-neutral-dark focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                                <p class="text-[10px] text-neutral-muted italic">* Maksimal durasi peminjaman fisik adalah {{ $physicalDays }} hari kalender.</p>
                                            </div>

                                            <x-button type="submit" variant="secondary" class="w-full py-3 text-xs uppercase tracking-wider font-semibold">
                                                Pinjam Buku Fisik (Ambil di Perpustakaan)
                                            </x-button>
                                        </form>
                                    @else
                                        <!-- Stock exhausted -->
                                        <div class="p-3.5 bg-red-50 rounded border border-red-200 text-xs text-danger space-y-1">
                                            <p class="font-semibold">Stok Fisik Sedang Tidak Tersedia</p>
                                            <p class="text-neutral-body">
                                                Seluruh eksemplar fisik buku ini sedang dipinjam oleh anggota lain.
                                                @if($book->hasDigital())
                                                    Versi digital tersedia dan dapat dipinjam untuk dibaca melalui website di atas.
                                                @endif
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            @endif

                        @else
                            <!-- Admin management shortcut -->
                            <div class="p-5 bg-neutral-surface rounded-lg border border-neutral-border text-center space-y-3">
                                <span class="text-[10px] font-bold text-primary uppercase tracking-widest block">Panel Kontrol Administrator</span>
                                <p class="text-xs text-neutral-body">Anda membuka halaman ini sebagai Administrator perpustakaan.</p>
                                <div class="flex flex-wrap justify-center gap-3">
                                    <a href="{{ route('admin.books.edit', $book) }}" class="btn-editorial text-xs py-2 px-4">
                                        Edit Data & Berkas PDF
                                    </a>
                                    @if($book->hasDigital())
                                        <a href="{{ route('admin.books.reader', $book) }}" class="btn-editorial-outline text-xs py-2 px-4">
                                            Pratinjau Dokumen Digital
                                        </a>
                                    @endif
                                    <a href="{{ route('admin.loans.create') }}" class="btn-editorial-outline text-xs py-2 px-4">
                                        Catat Peminjaman Anggota
                                    </a>
                                </div>
                            </div>
                        @endif
                    @else
                        <!-- Guest Prompt -->
                        <div class="p-6 bg-neutral-surface rounded-lg border border-neutral-border text-center space-y-4">
                            <div>
                                <h4 class="font-sans text-lg font-bold text-neutral-dark">Tertarik Meminjam Koleksi Ini?</h4>
                                <p class="text-xs text-neutral-body mt-1">Silakan masuk ke akun anggota Anda atau daftarkan diri untuk melakukan peminjaman digital maupun fisik.</p>
                            </div>
                            <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                                <a href="{{ route('login') }}" class="btn-editorial py-2.5 px-6 text-xs uppercase tracking-wider w-full sm:w-auto">
                                    Masuk ke Akun
                                </a>
                                <a href="{{ route('register') }}" class="btn-editorial-outline py-2.5 px-6 text-xs uppercase tracking-wider w-full sm:w-auto">
                                    Daftar Anggota Baru
                                </a>
                            </div>
                        </div>
                    @endauth
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
