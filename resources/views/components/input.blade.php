@props(['disabled' => false, 'label' => '', 'error' => ''])

<div>
    @if($label)
        <label class="block text-xs font-semibold text-[#666666] uppercase tracking-wider mb-2 px-0.5">
            {{ $label }}
        </label>
    @endif

    <div class="relative">
        <input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'w-full px-3.5 py-2.5 bg-white border border-neutral-border rounded-md text-sm font-normal text-neutral-dark placeholder:text-neutral-muted focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all disabled:opacity-50 disabled:bg-[#F8F8F7] shadow-xs']) !!}>
    </div>

    @if($error)
        <p class="mt-1.5 text-xs font-medium text-danger px-0.5">{{ $error }}</p>
    @endif
</div>
