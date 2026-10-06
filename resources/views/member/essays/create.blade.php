<x-app-layout>
    <x-slot name="header">
        Pengajuan Karya Tulis & Esai — RPK PUSTAKA IMM SAINTEKMU
    </x-slot>

    <div class="space-y-6 animate-in fade-in duration-300" x-data="{ mode: '{{ old('submission_type', 'online') }}' }">
        <div>
            <a href="{{ route('anggota.essays.index') }}" class="inline-flex items-center text-xs font-semibold uppercase tracking-wider text-neutral-muted hover:text-primary transition-colors">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke Daftar Tulisan Saya
            </a>
        </div>

        <x-card>
            <div class="p-2 space-y-6">
                <div class="space-y-1 pb-4 border-b border-neutral-border">
                    <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-primary">Formulir Pengajuan Naskah</span>
                    <h2 class="font-serif text-2xl font-normal text-neutral-dark">Kirimkan Esai Anda</h2>
                    <p class="text-xs text-neutral-body">Pilih metode penulisan yang Anda kehendaki: menulis langsung di website atau mengunggah berkas naskah PDF/DOCX.</p>
                </div>

                <form action="{{ route('anggota.essays.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <!-- Title -->
                    <div>
                        <label class="block text-xs font-semibold text-neutral-dark uppercase tracking-wider mb-2">Judul Esai / Karya Tulis</label>
                        <input type="text" name="title" value="{{ old('title') }}" required placeholder="Contoh: Rekonstruksi Paradigma Pendidikan di Era Kecerdasan Artifisial..." 
                            class="w-full px-3.5 py-2.5 bg-white border border-neutral-border rounded-md text-sm text-neutral-dark focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                        <x-input-error :messages="$errors->get('title')" class="mt-1" />
                    </div>

                    <!-- Submission Type Selection -->
                    <div>
                        <label class="block text-xs font-semibold text-neutral-dark uppercase tracking-wider mb-2">Pilih Cara Pengajuan:</label>
                        <div class="grid grid-cols-2 gap-4">
                            <label @click="mode = 'online'" 
                                   :class="mode === 'online' ? 'border-primary bg-primary-light/40 text-primary' : 'border-neutral-border bg-white text-neutral-body hover:border-neutral-dark'"
                                   class="p-4 border rounded-lg cursor-pointer flex items-center space-x-3 transition-all">
                                <input type="radio" name="submission_type" value="online" x-model="mode" class="text-primary focus:ring-primary">
                                <div>
                                    <span class="font-semibold text-xs block">Tulis Langsung</span>
                                    <span class="text-[10px] text-neutral-muted">Ketik naskah di formulir web</span>
                                </div>
                            </label>

                            <label @click="mode = 'file'" 
                                   :class="mode === 'file' ? 'border-primary bg-primary-light/40 text-primary' : 'border-neutral-border bg-white text-neutral-body hover:border-neutral-dark'"
                                   class="p-4 border rounded-lg cursor-pointer flex items-center space-x-3 transition-all">
                                <input type="radio" name="submission_type" value="file" x-model="mode" class="text-primary focus:ring-primary">
                                <div>
                                    <span class="font-semibold text-xs block">Unggah Berkas</span>
                                    <span class="text-[10px] text-neutral-muted">PDF atau Microsoft Word (DOCX)</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Online Writing Area -->
                    <div x-show="mode === 'online'" class="space-y-2">
                        <label class="block text-xs font-semibold text-neutral-dark uppercase tracking-wider">Isi Naskah Esai</label>
                        <textarea name="content" rows="12" placeholder="Tuliskan gagasan dan argumen esai Anda secara mendalam di sini..."
                            class="w-full px-3.5 py-3 bg-white border border-neutral-border rounded-md text-sm text-neutral-dark focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary leading-relaxed font-sans">{{ old('content') }}</textarea>
                        <p class="text-[10px] text-neutral-muted">* Format teks mendukung pemisahan paragraf standar.</p>
                        <x-input-error :messages="$errors->get('content')" class="mt-1" />
                    </div>

                    <!-- File Upload Area -->
                    <div x-show="mode === 'file'" class="space-y-2">
                        <label class="block text-xs font-semibold text-neutral-dark uppercase tracking-wider">Pilih Berkas Dokumen (PDF / DOCX)</label>
                        <div class="p-6 bg-neutral-surface border-2 border-dashed border-neutral-border rounded-lg text-center space-y-2">
                            <svg class="w-8 h-8 text-neutral-muted mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <p class="text-xs text-neutral-body">Pilih berkas dari perangkat Anda (Maksimal 10MB)</p>
                            <input type="file" name="essay_file" accept=".pdf,.docx,.doc" class="w-full text-xs text-neutral-dark file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-primary file:text-white cursor-pointer bg-white p-2 border border-neutral-border rounded">
                        </div>
                        <p class="text-[10px] text-neutral-muted">* Berkas akan disimpan di storage privat yang terproteksi dan hanya dapat diakses oleh Anda serta tim kurator.</p>
                        <x-input-error :messages="$errors->get('essay_file')" class="mt-1" />
                    </div>

                    <div class="pt-6 border-t border-neutral-border flex items-center justify-end space-x-3">
                        <a href="{{ route('anggota.essays.index') }}" class="btn-editorial-outline text-xs py-2 px-4 uppercase tracking-wider">Batal</a>
                        <x-button type="submit" variant="primary" class="text-xs py-2.5 px-6 uppercase tracking-wider font-semibold">Kirimkan Naskah</x-button>
                    </div>
                </form>
            </div>
        </x-card>
    </div>
</x-app-layout>
