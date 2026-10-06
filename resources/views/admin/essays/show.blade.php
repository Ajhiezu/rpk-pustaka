<x-app-layout>
    <x-slot name="header">
        Lembar Telaah Esai, {{ $essay->title }}
    </x-slot>

    <div class="space-y-8 animate-in fade-in duration-300">
        <div>
            <a href="{{ route('admin.essays.index') }}" class="inline-flex items-center text-xs font-semibold uppercase tracking-wider text-neutral-muted hover:text-primary transition-colors">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke Antrean Kurasi Esai
            </a>
        </div>

        <!-- Submission Metadata Card -->
        <div class="bg-white p-6 sm:p-8 rounded-lg border border-neutral-border shadow-xs space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-neutral-border">
                <div class="space-y-1">
                    <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-primary">Naskah Pengajuan Anggota</span>
                    <h1 class="font-serif text-2xl sm:text-3xl font-normal text-neutral-dark tracking-tight">
                        {{ $essay->title }}
                    </h1>
                    <p class="text-xs text-neutral-muted">
                        Diajukan oleh: <strong class="text-neutral-dark">{{ $essay->user->name }}</strong> ({{ $essay->user->email }}) • {{ $essay->created_at->format('d F Y') }}
                    </p>
                </div>
                <div>
                    @if($essay->status === 'submitted')
                        <x-badge variant="indigo" class="px-3 py-1 text-xs">Menunggu Review</x-badge>
                    @elseif($essay->status === 'approved')
                        <x-badge variant="emerald" class="px-3 py-1 text-xs">Disetujui</x-badge>
                    @elseif($essay->status === 'published')
                        <x-badge variant="primary" class="px-3 py-1 text-xs">Published</x-badge>
                    @elseif($essay->status === 'revision')
                        <x-badge variant="amber" class="px-3 py-1 text-xs">Perlu Revisi</x-badge>
                    @elseif($essay->status === 'rejected')
                        <x-badge variant="rose" class="px-3 py-1 text-xs">Ditolak</x-badge>
                    @endif
                </div>
            </div>

            <!-- Content Area: Online Text OR File Download -->
            <div class="space-y-4">
                <h3 class="text-xs font-bold uppercase tracking-[0.2em] text-neutral-muted">Konten & Naskah</h3>

                @if($essay->isOnline())
                    <div class="p-6 bg-neutral-surface rounded-lg border border-neutral-border prose max-w-none text-sm text-neutral-dark leading-relaxed font-sans">
                        {!! nl2br(e($essay->content)) !!}
                    </div>
                @else
                    <div class="p-6 bg-neutral-surface rounded-lg border border-neutral-border flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 rounded bg-white border border-neutral-border flex items-center justify-center text-primary font-bold shadow-xs">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-sm text-neutral-dark">Berkas Naskah Unggahan Anggota</h4>
                                <p class="text-xs text-neutral-muted">Format berkas: {{ strtoupper($essay->file_type ?? 'Dokumen') }} • Tersimpan aman di private storage</p>
                            </div>
                        </div>

                        <a href="{{ route('admin.essays.download', $essay) }}" class="btn-editorial py-2.5 px-5 text-xs uppercase tracking-wider font-semibold inline-flex items-center gap-1.5 shadow-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            Unduh & Periksa Berkas
                        </a>
                    </div>
                @endif
            </div>

            <!-- Existing Review History if any -->
            @if($essay->review_note)
                <div class="p-4 bg-amber-50 rounded-lg border border-amber-200 space-y-1">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-[#B45309] block">Catatan Kurator Sebelumnya</span>
                    <p class="text-xs text-neutral-dark leading-relaxed">{{ $essay->review_note }}</p>
                    <p class="text-[10px] text-neutral-muted pt-1">Ditinjau oleh: {{ $essay->reviewer->name ?? 'Admin' }} pada {{ $essay->reviewed_at?->format('d M Y') }}</p>
                </div>
            @endif
        </div>

        <!-- Moderation & Decision Form -->
        <x-card>
            <div class="p-2 space-y-5">
                <div>
                    <h3 class="font-serif text-lg font-semibold text-neutral-dark">Keputusan Kuratorial Administrator</h3>
                    <p class="text-xs text-neutral-muted mt-0.5">Tentukan status pengajuan naskah dan berikan catatan evaluasi untuk penulis.</p>
                </div>

                <form action="{{ route('admin.essays.updateStatus', $essay) }}" method="POST" class="space-y-5">
                    @csrf

                    <div>
                        <label class="block text-xs font-semibold text-neutral-dark uppercase tracking-wider mb-2">Tindakan Evaluasi:</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                            <label class="p-3.5 border rounded-lg cursor-pointer flex flex-col space-y-1 transition-all {{ $essay->status === 'approved' ? 'border-success bg-green-50' : 'border-neutral-border hover:border-success/60' }}">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-success uppercase">Setujui (Approve)</span>
                                    <input type="radio" name="status" value="approved" {{ $essay->status === 'approved' ? 'checked' : '' }} class="text-success focus:ring-success">
                                </div>
                                <span class="text-[10px] text-neutral-muted">Naskah memenuhi kualifikasi kurasi.</span>
                            </label>

                            <label class="p-3.5 border rounded-lg cursor-pointer flex flex-col space-y-1 transition-all {{ $essay->status === 'published' ? 'border-primary bg-primary-light' : 'border-neutral-border hover:border-primary/60' }}">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-primary uppercase">Publikasikan</span>
                                    <input type="radio" name="status" value="published" {{ $essay->status === 'published' ? 'checked' : '' }} class="text-primary focus:ring-primary">
                                </div>
                                <span class="text-[10px] text-neutral-muted">Terbitkan ke publik dan katalog.</span>
                            </label>

                            <label class="p-3.5 border rounded-lg cursor-pointer flex flex-col space-y-1 transition-all {{ $essay->status === 'revision' ? 'border-amber-400 bg-amber-50' : 'border-neutral-border hover:border-amber-400/60' }}">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-[#B45309] uppercase">Minta Revisi</span>
                                    <input type="radio" name="status" value="revision" {{ $essay->status === 'revision' ? 'checked' : '' }} class="text-amber-500 focus:ring-amber-500">
                                </div>
                                <span class="text-[10px] text-neutral-muted">Minta anggota memperbaiki tulisan.</span>
                            </label>

                            <label class="p-3.5 border rounded-lg cursor-pointer flex flex-col space-y-1 transition-all {{ $essay->status === 'rejected' ? 'border-danger bg-red-50' : 'border-neutral-border hover:border-danger/60' }}">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-danger uppercase">Tolak Naskah</span>
                                    <input type="radio" name="status" value="rejected" {{ $essay->status === 'rejected' ? 'checked' : '' }} class="text-danger focus:ring-danger">
                                </div>
                                <span class="text-[10px] text-neutral-muted">Naskah tidak memenuhi syarat.</span>
                            </label>
                        </div>
                        <x-input-error :messages="$errors->get('status')" class="mt-1.5" />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-neutral-dark uppercase tracking-wider mb-2">Catatan Kuratorial / Catatan Revisi:</label>
                        <textarea name="review_note" rows="4" placeholder="Tuliskan catatan apresiasi, masukan kurasi, atau bagian yang perlu direvisi oleh anggota..."
                            class="w-full px-3.5 py-2.5 bg-white border border-neutral-border rounded-md text-sm text-neutral-dark focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">{{ old('review_note', $essay->review_note) }}</textarea>
                        <p class="text-[10px] text-neutral-muted mt-1">* Catatan ini dapat dibaca langsung oleh anggota pada panel karya tulis mereka.</p>
                        <x-input-error :messages="$errors->get('review_note')" class="mt-1" />
                    </div>

                    <div class="pt-4 border-t border-neutral-border flex items-center justify-end space-x-3">
                        <a href="{{ route('admin.essays.index') }}" class="btn-editorial-outline text-xs py-2 px-4 uppercase tracking-wider">Kembali</a>
                        <x-button type="submit" variant="primary" class="text-xs py-2.5 px-6 uppercase tracking-wider font-semibold">Simpan Keputusan Kurasi</x-button>
                    </div>
                </form>
            </div>
        </x-card>
    </div>
</x-app-layout>
