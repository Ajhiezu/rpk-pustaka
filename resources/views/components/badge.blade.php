@props([
    'variant' => 'slate',
])

@php
    $variants = [
        'slate' => 'bg-[#F8F8F7] text-[#666666] border border-[#E5E5E5]',
        'primary' => 'bg-[#FEF2F2] text-primary border border-[#FECACA]',
        'accent' => 'bg-[#FFF9ED] text-accent border border-[#FDE68A]',
        'indigo' => 'bg-[#E3F2FD] text-[#1976D2] border border-[#BBDEFB]',
        'blue' => 'bg-[#E3F2FD] text-[#1976D2] border border-[#BBDEFB]',
        'emerald' => 'bg-[#EDF7ED] text-[#2E7D32] border border-[#C8E6C9]',
        'rose' => 'bg-[#FDEDED] text-[#D32F2F] border border-[#FFCDD2]',
        'amber' => 'bg-[#FFF8E1] text-[#B78103] border border-[#FFE082]',
    ];
    $badgeClass = $variants[$variant] ?? $variants['slate'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded text-[11px] font-semibold uppercase tracking-wider {$badgeClass}"]) }}>
    {{ $slot }}
</span>
