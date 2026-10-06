<x-app-layout>
    <x-slot name="header">
        Karya Tulis Anggota, {{ $essay->title }}
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-8 animate-in fade-in duration-300">
        <div>
            <a href="{{ Auth::check() ? route('anggota.essays.index') : url('/') }}" class="inline-flex items-center text-xs font-semibold uppercase tracking-wider text-neutral-muted hover:text-primary transition-colors">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                {{ Auth::check() ? 'Kembali ke Daftar Tulisan' : 'Kembali ke Beranda Pustaka' }}
            </a>
        </div>

        <!-- Status & Review Callout if Author is viewing -->
        @if(Auth::id() === $essay->user_id)
            <div class="p-5 rounded-lg border {{ $essay->status === 'approved' || $essay->status === 'published' ? 'bg-[#EDF7ED] border-[#C8E6C9]' : ($essay->status === 'revision' ? 'bg-[#FFF9ED] border-[#FDE68A]' : ($essay->status === 'rejected' ? 'bg-red-50 border-red-200' : 'bg-neutral-surface border-neutral-border')) }} space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-neutral-dark">Status Kurasi Pengajuan Naskah Anda</span>
                    @if($essay->status === 'submitted')
                        <x-badge variant="indigo">Menunggu Review</x-badge>
                    @elseif($essay->status === 'approved')
                        <x-badge variant="emerald">Disetujui Kurator</x-badge>
                    @elseif($essay->status === 'published')
                        <x-badge variant="primary">Telah Dipublikasikan</x-badge>
                    @elseif($essay->status === 'revision')
                        <x-badge variant="amber">Perlu Revisi</x-badge>
                    @elseif($essay->status === 'rejected')
                        <x-badge variant="rose">Ditolak</x-badge>
                    @endif
                </div>

                @if($essay->review_note)
                    <div class="pt-2 border-t border-neutral-border/50 text-xs text-neutral-dark">
                        <strong>Catatan Kurator:</strong>
                        <p class="mt-1 italic leading-relaxed">{{ $essay->review_note }}</p>
                    </div>
                @endif
            </div>
        @endif

        <!-- Essay Reading Card -->
        <article class="bg-white p-8 sm:p-12 rounded-lg border border-neutral-border shadow-xs space-y-8">
            <div class="space-y-3 pb-6 border-b border-neutral-border text-center max-w-2xl mx-auto">
                <span class="px-2.5 py-0.5 bg-neutral-surface text-neutral-dark text-[10px] font-bold uppercase tracking-wider rounded border border-neutral-border">
                    Esai & Karya Tulis Anggota
                </span>

                <h1 class="font-serif text-3xl sm:text-4xl font-normal text-neutral-dark tracking-tight leading-tight">
                    {{ $essay->title }}
                </h1>

                <div class="flex items-center justify-center gap-3 text-xs text-neutral-muted pt-2">
                    <span class="font-medium text-neutral-dark">Penulis: {{ $essay->user->name }}</span>
                    <span>•</span>
                    <span>{{ $essay->created_at->format('d F Y') }}</span>
                </div>
            </div>

            <!-- Content Area -->
            @if($essay->isOnline())
                <div class="prose max-w-none text-neutral-dark leading-relaxed space-y-5 text-sm sm:text-base font-normal">
                    {!! nl2br(e($essay->content)) !!}
                </div>
            @else
                <div class="p-8 bg-neutral-surface rounded-lg border border-neutral-border text-center space-y-4">
                    <div class="w-16 h-16 rounded-full bg-white border border-neutral-border flex items-center justify-center text-primary mx-auto shadow-xs">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <div>
                        <h4 class="font-serif text-lg font-semibold text-neutral-dark">Naskah Terlampir dalam Berkas Dokumen</h4>
                        <p class="text-xs text-neutral-muted mt-1">Naskah diajukan dalam format {{ strtoupper($essay->file_type ?? 'Dokumen') }} melalui unggahan privat terproteksi.</p>
                    </div>

                    <div>
                        <a href="{{ route('anggota.essays.download', $essay) }}" class="btn-editorial py-2.5 px-6 text-xs uppercase tracking-wider font-semibold inline-flex items-center gap-2 shadow-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            Unduh & Baca Naskah Dokumen
                        </a>
                    </div>
                </div>
            @endif

            <div class="pt-8 border-t border-neutral-border flex items-center justify-between text-xs text-neutral-muted">
                <span>RPK PUSTAKA IMM SAINTEKMU, Ruang Karya Literasi Anggota</span>
                <a href="{{ Auth::check() ? route('anggota.essays.index') : url('/') }}" class="font-semibold text-primary hover:underline">
                    &larr; {{ Auth::check() ? 'Kembali ke Daftar Tulisan' : 'Kembali ke Beranda Utama' }}
                </a>
            </div>
        </article>
    </div>
</x-app-layout>
