@props(['type' => 'info'])

@php
    $variants = [
        'info' => 'bg-[#E3F2FD] text-[#1565C0] border-[#BBDEFB]',
        'success' => 'bg-[#EDF7ED] text-[#2E7D32] border-[#C8E6C9]',
        'warning' => 'bg-[#FFF8E1] text-[#B78103] border-[#FFE082]',
        'danger' => 'bg-[#FDEDED] text-primary border-[#FFCDD2]',
    ];

    $icons = [
        'info' => '<svg class="w-5 h-5 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
        'success' => '<svg class="w-5 h-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
        'warning' => '<svg class="w-5 h-5 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>',
        'danger' => '<svg class="w-5 h-5 text-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>',
    ];

    $alertClasses = $variants[$type] ?? $variants['info'];
    $alertIcon = $icons[$type] ?? $icons['info'];
@endphp

<div {{ $attributes->merge(['class' => "flex items-start p-4 rounded-md border {$alertClasses} transition-all duration-200 shadow-xs"]) }}>
    <div class="flex-shrink-0 mt-0.5">
        {!! $alertIcon !!}
    </div>
    <div class="ml-3">
        <div class="text-sm font-medium">
            {{ $slot }}
        </div>
    </div>
</div>

