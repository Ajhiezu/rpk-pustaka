<x-app-layout>
    <x-slot name="header">
        Publikasi Ilmiah — {{ $article->title }}
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-8 animate-in fade-in duration-300">
        <div>
            <a href="{{ Auth::check() ? route('anggota.articles.index') : route('public.articles.index') }}" 
               class="inline-flex items-center text-xs font-semibold uppercase tracking-wider text-neutral-muted hover:text-primary transition-colors">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke Arsip Artikel
            </a>
        </div>

        <article class="bg-white p-8 sm:p-12 rounded-lg border border-neutral-border shadow-xs space-y-8">
            <!-- Article Header -->
            <div class="space-y-4 pb-6 border-b border-neutral-border text-center max-w-2xl mx-auto">
                <span class="px-2.5 py-0.5 bg-primary-light text-primary text-[10px] font-bold uppercase tracking-wider rounded border border-red-200">
                    Artikel Ilmiah RPK PUSTAKA IMM SAINTEK MU
                </span>

                <h1 class="font-serif text-3xl sm:text-4xl font-normal text-neutral-dark tracking-tight leading-tight">
                    {{ $article->title }}
                </h1>

                <div class="flex items-center justify-center gap-3 text-xs text-neutral-muted pt-2">
                    <span class="font-medium text-neutral-dark">{{ $article->user->name ?? 'Dewan Redaksi' }}</span>
                    <span>•</span>
                    <span>{{ $article->published_at ? $article->published_at->translatedFormat('d F Y') : '' }}</span>
                </div>
            </div>

            <!-- Optional Cover -->
            @if($article->cover_image)
                <div class="aspect-[21/9] rounded-lg overflow-hidden border border-neutral-border shadow-xs">
                    <img src="{{ asset('storage/'.$article->cover_image) }}" alt="{{ $article->title }}" class="w-full h-full object-cover">
                </div>
            @endif

            <!-- Excerpt Callout -->
            @if($article->excerpt)
                <div class="p-5 bg-neutral-surface rounded-lg border-l-4 border-primary text-sm font-serif italic text-neutral-dark leading-relaxed">
                    {{ $article->excerpt }}
                </div>
            @endif

            <!-- Body Content -->
            <div class="prose max-w-none text-neutral-dark leading-relaxed space-y-5 text-sm sm:text-base font-normal">
                {!! nl2br(e($article->content)) !!}
            </div>

            <!-- Footer Author Sign-off -->
            <div class="pt-8 border-t border-neutral-border flex items-center justify-between text-xs text-neutral-muted">
                <span>Diterbitkan oleh Perpustakaan RPK PUSTAKA IMM SAINTEK MU</span>
                <a href="{{ Auth::check() ? route('anggota.articles.index') : route('public.articles.index') }}" class="font-semibold text-primary hover:underline">
                    &larr; Lihat Artikel Lainnya
                </a>
            </div>
        </article>

        <!-- Related Articles Recommendations -->
        @if(isset($latestArticles) && $latestArticles->count() > 0)
            <div class="space-y-4 pt-6">
                <h3 class="font-serif text-xl font-semibold text-neutral-dark">Artikel Terkait Lainnya</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($latestArticles as $rel)
                        <a href="{{ Auth::check() ? route('anggota.articles.show', $rel) : route('public.articles.show', $rel->slug) }}" class="p-4 bg-white rounded-lg border border-neutral-border hover:border-primary/50 transition-colors block group">
                            <span class="text-[10px] text-neutral-muted block mb-1">{{ $rel->published_at?->format('d M Y') }}</span>
                            <h4 class="font-serif text-sm font-semibold text-neutral-dark group-hover:text-primary transition-colors line-clamp-1">
                                {{ $rel->title }}
                            </h4>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
