<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? 'Autentikasi' }} - RPK PUSTAKA IMM SAINTEKMU</title>
        
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
    <body class="antialiased bg-[#F8F8F7] md:bg-white text-neutral-dark selection:bg-primary/10 selection:text-primary min-h-screen">
        <div class="min-h-screen flex flex-col md:flex-row bg-[#F8F8F7] md:bg-white">
            
            <!-- Desktop Brand Showcase Side (Visible on md+) -->
            <div class="hidden md:flex md:w-1/2 bg-[#C62828] text-white flex-col justify-between p-12 lg:p-16 relative overflow-hidden">
                <!-- Decorative background elements -->
                <div class="absolute -right-20 -top-20 w-80 h-80 rounded-full bg-white/5 blur-2xl pointer-events-none"></div>
                <div class="absolute -left-20 -bottom-20 w-80 h-80 rounded-full bg-black/10 blur-2xl pointer-events-none"></div>

                <!-- Top Institution Title with official RPK PUSTAKA IMM SAINTEKMU logo -->
                <div class="relative z-10">
                    <a href="{{ url('/') }}" class="inline-flex items-center space-x-3.5 group">
                        <div class="bg-white p-2.5 rounded-xl shadow-md flex items-center justify-center shrink-0 transition-transform group-hover:scale-105">
                            <img src="{{ asset('images/logo-rpk.png') }}" alt="RPK PUSTAKA IMM SAINTEKMU" class="h-12 w-auto object-contain">
                        </div>
                        <div>
                            <span class="font-sans text-xl font-bold tracking-tight text-white block leading-none">RPK PUSTAKA</span>
                            <span class="text-[10px] uppercase tracking-wider text-white/90 font-semibold block mt-1.5 whitespace-nowrap">IMM SAINTEKMU</span>
                        </div>
                    </a>
                </div>
                
                <!-- Middle Quote with gold detail -->
                <div class="relative z-10 max-w-md my-auto py-12">
                    <div class="flex items-center gap-2 mb-4">
                        <svg class="w-4 h-4 text-accent fill-current shrink-0" viewBox="0 0 24 24">
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

            <!-- Mobile Brand Banner (Visible on mobile screens < md) -->
            <div class="block md:hidden bg-[#C62828] text-white pt-10 pb-14 px-6 relative overflow-hidden shadow-md rounded-b-[2rem]">
                <!-- Background ambient circles -->
                <div class="absolute right-0 top-0 w-48 h-48 rounded-full bg-white/5 blur-xl pointer-events-none"></div>
                <div class="absolute left-0 bottom-0 w-36 h-36 rounded-full bg-black/10 blur-xl pointer-events-none"></div>

                <div class="relative z-10 flex flex-col items-center text-center">
                    <a href="{{ url('/') }}" class="inline-flex flex-col items-center group mb-3">
                        <div class="bg-white p-2.5 rounded-xl shadow-lg flex items-center justify-center mb-3">
                            <img src="{{ asset('images/logo-rpk.png') }}" alt="RPK PUSTAKA IMM SAINTEKMU" class="h-12 w-auto object-contain">
                        </div>
                        <span class="font-sans text-xl font-bold tracking-tight text-white leading-none">RPK PUSTAKA</span>
                        <span class="text-[11px] uppercase tracking-widest text-accent font-semibold mt-1.5">IMM SAINTEKMU</span>
                    </a>
                    
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-white/10 rounded-full text-white/90 text-[11px] font-medium backdrop-blur-xs mt-1">
                        <svg class="w-3 h-3 text-accent fill-current shrink-0" viewBox="0 0 24 24">
                            <path d="M12 2l2.4 7.2h7.6l-6.2 4.5 2.4 7.3-6.2-4.6-6.2 4.6 2.4-7.3-6.2-4.5h7.6z"/>
                        </svg>
                        <span>Perpustakaan Digital Akademik</span>
                    </div>
                </div>
            </div>

            <!-- Form Container Section -->
            <div class="flex-1 flex items-center justify-center px-4 py-6 sm:p-8 md:p-16 lg:p-20 bg-[#F8F8F7] md:bg-white -mt-7 md:mt-0 relative z-20">
                <div class="w-full max-w-md bg-white md:bg-transparent p-6 sm:p-8 md:p-0 rounded-2xl md:rounded-none shadow-xl md:shadow-none border border-neutral-border/80 md:border-none">
                    @yield('content')
                </div>
            </div>

        </div>
    </body>
</html>
