@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-xs font-semibold text-[#5A4E47] uppercase tracking-wider mb-1.5']) }}>
    {{ $value ?? $slot }}
</label>

