@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'px-3.5 py-2.5 bg-white border border-neutral-border rounded-md text-sm text-neutral-dark placeholder:text-neutral-muted focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all disabled:opacity-50 disabled:bg-[#F8F8F7] shadow-xs']) }}>
