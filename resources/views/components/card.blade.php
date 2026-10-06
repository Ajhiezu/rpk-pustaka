<div {{ $attributes->merge(['class' => 'bg-white rounded-lg border border-neutral-border shadow-xs transition-all duration-200']) }}>
    @if(isset($header))
        <div class="px-6 py-4 border-b border-neutral-border bg-[#F8F8F7] rounded-t-lg border-l-4 border-l-primary flex items-center justify-between">
            <h3 class="font-sans text-neutral-dark font-bold text-lg tracking-tight">{{ $header }}</h3>
        </div>
    @endif
    
    <div class="p-6">
        {{ $slot }}
    </div>

    @if(isset($footer))
        <div class="px-6 py-4 border-t border-neutral-border bg-[#F8F8F7] rounded-b-lg">
            {{ $footer }}
        </div>
    @endif
</div>
