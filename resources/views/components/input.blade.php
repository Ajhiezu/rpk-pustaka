@props(['disabled' => false, 'label' => '', 'error' => ''])

@php
    $isPassword = ($attributes->get('type') === 'password');
    $inputId = $attributes->get('id', 'password_' . Str::random(5));
@endphp

<div>
    @if($label)
        <label class="block text-xs font-semibold text-[#666666] uppercase tracking-wider mb-2 px-0.5" @if($attributes->has('id')) for="{{ $attributes->get('id') }}" @endif>
            {{ $label }}
        </label>
    @endif

    <div class="relative">
        <input
            {{ $disabled ? 'disabled' : '' }}
            {!! $attributes->merge([
                'class' => 'w-full px-3.5 py-2.5 bg-white border border-neutral-border rounded-md text-sm font-normal text-neutral-dark placeholder:text-neutral-muted focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all disabled:opacity-50 disabled:bg-[#F8F8F7] shadow-xs' . ($isPassword ? ' pr-10' : ''),
                'id' => $inputId,
            ]) !!}
        >
        @if($isPassword)
            <button
                type="button"
                data-password-toggle
                data-target="{{ $inputId }}"
                class="password-toggle-btn absolute inset-y-0 right-0 flex items-center px-3 text-neutral-muted hover:text-neutral-dark focus:outline-none transition-colors"
                aria-label="Tampilkan kata sandi"
            >
                <svg class="password-toggle-icon-eye-off w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                </svg>
                <svg class="password-toggle-icon-eye w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
            </button>
        @endif
    </div>

    @if($error)
        <p class="mt-1.5 text-xs font-medium text-danger px-0.5">{{ $error }}</p>
    @endif
</div>
