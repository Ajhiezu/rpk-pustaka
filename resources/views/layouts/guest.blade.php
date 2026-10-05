<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'RPK PUSTAKA IMM SAINTEK MU') }} — Modern Academic Editorial Library</title>

        <!-- Favicon -->
        <link rel="icon" type="image/x-icon" href="{{ asset('images/logo-rpk.ico') }}">
        <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/logo-rpk.ico') }}">

        <!-- Fonts: Inter Sans-Serif System -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body { font-family: 'Inter', system-ui, -apple-system, sans-serif; }
            .font-serif { font-family: 'Inter', system-ui, -apple-system, sans-serif; }
        </style>
    </head>
    <body class="font-sans text-neutral-dark bg-white antialiased selection:bg-primary/10 selection:text-primary">
        <div class="min-h-screen flex flex-col justify-center items-center py-12 px-6 bg-[#F8F8F7]">
            <div class="text-center mb-8">
                <a href="/" class="inline-flex flex-col items-center group">
                    <img src="{{ asset('images/logo-rpk.png') }}" alt="RPK PUSTAKA IMM SAINTEK MU" class="h-14 w-auto object-contain mb-3">
                    <span class="font-sans text-2xl font-bold tracking-tight text-neutral-dark block leading-none">RPK PUSTAKA</span>
                    <span class="text-[10px] font-semibold text-neutral-muted uppercase tracking-wider block mt-1.5">IMM SAINTEK MU</span>
                </a>
            </div>

            <div class="w-full sm:max-w-md p-8 bg-white border border-neutral-border rounded-lg shadow-xs">
                {{ $slot }}
            </div>

            <div class="mt-8 text-center text-xs text-neutral-muted">
                <a href="/" class="hover:text-primary transition-colors">&larr; Kembali ke Beranda Perpustakaan</a>
            </div>
        </div>
    </body>
</html>
