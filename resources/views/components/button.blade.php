@props([
    'variant' => 'primary',
    'type' => 'button',
    'size' => 'md'
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-semibold rounded-md transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer tracking-wide';
    
    $variants = [
        'primary' => 'bg-primary text-white hover:bg-primary-hover focus:ring-primary/30 shadow-xs border border-primary',
        'secondary' => 'bg-white border border-primary text-primary hover:bg-primary-light focus:ring-primary/20 shadow-xs',
        'outline' => 'bg-white border border-neutral-border text-neutral-dark hover:border-primary hover:text-primary hover:bg-neutral-surface focus:ring-primary/20 shadow-xs',
        'accent' => 'bg-accent text-white hover:bg-accent-hover focus:ring-accent/30 shadow-xs border border-accent',
        'danger' => 'bg-danger text-white hover:bg-red-800 focus:ring-danger/30 shadow-xs border border-danger',
        'success' => 'bg-success text-white hover:bg-green-800 focus:ring-success/30 shadow-xs border border-success',
        'ghost' => 'bg-transparent text-neutral-body hover:bg-neutral-surface hover:text-neutral-dark focus:ring-primary/20',
    ];

    $sizes = [
        'sm' => 'px-3 py-1.5 text-xs',
        'md' => 'px-4 py-2.5 text-sm',
        'lg' => 'px-6 py-3 text-base',
    ];
@endphp

<button {{ $attributes->merge(['type' => $type, 'class' => "$baseClasses {$variants[$variant]} {$sizes[$size]}"]) }}>
    {{ $slot }}
</button>
