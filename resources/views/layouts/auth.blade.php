<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? 'Autentikasi' }}, RPK PUSTAKA IMM SAINTEKMU</title>
        
        <!-- Favicon -->
        <link rel="icon" type="image/x-icon" href="{{ asset('images/logo-rpk.ico') }}">
        <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/logo-rpk.ico') }}">

        <!-- Fonts: Inter Sans-Serif System -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            [x-cloak] { display: none !important; }
            body { font-family: 'Inter', system-ui, -apple-system, sans-serif; }
            .font-serif { font-family: 'Inter', system-ui, -apple-system, sans-serif; }
        </style>
    </head>
    <body class="antialiased bg-white text-neutral-dark selection:bg-primary/10 selection:text-primary">
        <div class="min-h-screen flex flex-col md:flex-row bg-white">
            <!-- Scholarly Brand Showcase Side (Red Background + White Text, Original Proportions) -->
            <div class="hidden md:flex md:w-1/2 bg-[#C62828] text-white flex-col justify-between p-16 relative overflow-hidden">
                <!-- Top Institution Title with official RPK PUSTAKA IMM SAINTEKMU logo -->
                <div class="relative z-10">
                    <a href="{{ url('/') }}" class="inline-flex items-center space-x-3.5 group">
                        <div class="bg-white p-2 rounded-lg shadow-sm flex items-center justify-center shrink-0">
                            <img src="{{ asset('images/logo-rpk.png') }}" alt="RPK PUSTAKA IMM SAINTEKMU" class="h-12 w-auto object-contain">
                        </div>
                        <div>
                            <span class="font-sans text-xl font-bold tracking-tight text-white block leading-none">RPK PUSTAKA</span>
                            <span class="text-[10px] uppercase tracking-wider text-white/90 font-semibold block mt-1.5 whitespace-nowrap">IMM SAINTEKMU</span>
                        </div>
                    </a>
                </div>
                
                <!-- Middle Quote with subtle gold detail -->
                <div class="relative z-10 max-w-md my-auto py-12">
                    <div class="flex items-center gap-2 mb-4">
                        <!-- Small Gold Star Accent from Logo -->
                        <svg class="w-4 h-4 text-accent fill-current" viewBox="0 0 24 24">
                            <path d="M12 2l2.4 7.2h7.6l-6.2 4.5 2.4 7.3-6.2-4.6-6.2 4.6 2.4-7.3-6.2-4.5h7.6z"/>
                        </svg>
                        <span class="text-xs font-semibold text-white tracking-wider uppercase">Membuka Gerbang Pengetahuan</span>
                    </div>
                    <blockquote class="font-sans text-2xl lg:text-3xl text-white leading-snug font-bold mb-6">
                        "Perpustakaan adalah ruang hening tempat pemikiran agung bertemu dengan mereka yang mencari kebenaran."
                    </blockquote>
                    <p class="text-sm sm:text-base text-white/90 leading-relaxed">
                        Akses ribuan naskah ilmiah, literatur referensi, dan fasilitas reservasi sirkulasi digital secara mandiri.
                    </p>
                </div>
                
                <!-- Bottom Scholarly Meta -->
                <div class="relative z-10 flex items-center justify-between text-xs text-white/75 tracking-wide uppercase font-semibold border-t border-white/20 pt-6">
                    <span>&copy; {{ date('Y') }} RPK PUSTAKA IMM SAINTEKMU</span>
                    <span class="text-white font-medium">Koleksi & Sirkulasi Terpadu</span>
                </div>
            </div>

            <!-- Form Side -->
            <div class="flex-1 flex items-center justify-center p-8 sm:p-14 md:p-20 bg-white">
                <div class="w-full max-w-md">
                    <!-- Mobile Logo -->
                    <div class="mb-10 flex items-center space-x-3 md:hidden">
                        <a href="{{ url('/') }}" class="inline-flex items-center space-x-3">
                            <img src="{{ asset('images/logo-rpk.png') }}" alt="RPK PUSTAKA IMM SAINTEKMU" class="h-10 w-auto object-contain">
                            <div>
                                <span class="font-sans text-lg font-bold text-neutral-dark block leading-none">RPK PUSTAKA</span>
                                <span class="text-[10px] uppercase tracking-wider text-primary font-semibold whitespace-nowrap">IMM SAINTEKMU</span>
                            </div>
                        </a>
                    </div>
                    
                    @yield('content')
                </div>
            </div>
        </div>
    </body>
</html>
