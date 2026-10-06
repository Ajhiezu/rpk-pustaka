<x-app-layout>
    <x-slot name="header">
        Edit Artikel, {{ $article->title }}
    </x-slot>

    <div class="space-y-6 animate-in fade-in duration-300">
        <div class="flex items-center justify-between">
            <a href="{{ route('admin.articles.index') }}" class="inline-flex items-center text-xs font-semibold uppercase tracking-wider text-neutral-muted hover:text-primary transition-colors">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke Daftar Artikel
            </a>
        </div>

        <x-card>
            <form action="{{ route('admin.articles.update', $article) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Title -->
                <div>
                    <label class="block text-xs font-semibold text-neutral-dark uppercase tracking-wider mb-2">Judul Artikel</label>
                    <input type="text" name="title" value="{{ old('title', $article->title) }}" required 
                        class="w-full px-3.5 py-2.5 bg-white border border-neutral-border rounded-md text-sm text-neutral-dark focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    <x-input-error :messages="$errors->get('title')" class="mt-1" />
                </div>

                <!-- Excerpt -->
                <div>
                    <label class="block text-xs font-semibold text-neutral-dark uppercase tracking-wider mb-2">Ringkasan / Abstrak Singkat</label>
                    <textarea name="excerpt" rows="2" 
                        class="w-full px-3.5 py-2 bg-white border border-neutral-border rounded-md text-sm text-neutral-dark focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">{{ old('excerpt', $article->excerpt) }}</textarea>
                    <x-input-error :messages="$errors->get('excerpt')" class="mt-1" />
                </div>

                <!-- Cover Image -->
                <div>
                    <label class="block text-xs font-semibold text-neutral-dark uppercase tracking-wider mb-2">Ganti Sampul Artikel (Opsional)</label>
                    @if($article->cover_image)
                        <div class="mb-3 flex items-center space-x-3">
                            <img src="{{ asset('storage/'.$article->cover_image) }}" class="h-16 w-24 object-cover rounded border border-neutral-border">
                            <span class="text-xs text-neutral-muted">Sampul saat ini</span>
                        </div>
                    @endif
                    <input type="file" name="cover_image" accept="image/*" class="w-full text-xs text-neutral-dark file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-primary file:text-white cursor-pointer bg-neutral-surface p-2 border border-neutral-border rounded">
                    <x-input-error :messages="$errors->get('cover_image')" class="mt-1" />
                </div>

                <!-- Content -->
                <div>
                    <label class="block text-xs font-semibold text-neutral-dark uppercase tracking-wider mb-2">Isi Konten Artikel</label>
                    <textarea name="content" rows="12" required 
                        class="w-full px-3.5 py-3 bg-white border border-neutral-border rounded-md text-sm text-neutral-dark focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary leading-relaxed font-sans">{{ old('content', $article->content) }}</textarea>
                    <x-input-error :messages="$errors->get('content')" class="mt-1" />
                </div>

                <!-- Status -->
                <div class="flex items-center space-x-4">
                    <label class="text-xs font-semibold text-neutral-dark uppercase tracking-wider">Status Publikasi:</label>
                    <label class="flex items-center space-x-2 text-xs cursor-pointer">
                        <input type="radio" name="status" value="published" {{ old('status', $article->status) === 'published' ? 'checked' : '' }} class="text-primary focus:ring-primary">
                        <span class="font-semibold text-success">Terbitkan (Published)</span>
                    </label>
                    <label class="flex items-center space-x-2 text-xs cursor-pointer">
                        <input type="radio" name="status" value="draft" {{ old('status', $article->status) === 'draft' ? 'checked' : '' }} class="text-primary focus:ring-primary">
                        <span class="text-neutral-muted">Simpan Sebagai Draft</span>
                    </label>
                </div>

                <div class="pt-6 border-t border-neutral-border flex items-center justify-end space-x-3">
                    <a href="{{ route('admin.articles.index') }}" class="btn-editorial-outline text-xs py-2 px-4 uppercase tracking-wider">Batal</a>
                    <x-button type="submit" variant="primary" class="text-xs py-2.5 px-6 uppercase tracking-wider font-semibold">Perbarui Artikel</x-button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
