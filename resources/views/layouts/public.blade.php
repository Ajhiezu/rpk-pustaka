<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'RPK PUSTAKA IMM SAINTEKMU') }}, Modern Academic Editorial Library</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('images/logo-rpk.ico') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/logo-rpk.ico') }}">

    <!-- Google Fonts: Inter Sans-Serif System -->
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

<body class="antialiased bg-white text-neutral-dark selection:bg-primary/10 selection:text-primary min-h-screen flex flex-col justify-between">

    <div>
        <!-- Institutional Top Bar -->
        <div class="bg-[#181818] text-[#E5E5E5] text-xs py-2.5 px-6 border-b border-[#262626]">
            <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2 font-medium">
                <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-3 items-center">
                    <span class="text-white font-semibold whitespace-nowrap">RPK PUSTAKA IMM SAINTEKMU</span>
                    <span class="hidden sm:inline text-accent">•</span>
                    <span class="hidden sm:inline text-[#A3A3A3]">Layanan Ruang Baca: Sen – Jum 06:00 – 00:00 WIB</span>
                </div>
                <div class="hidden sm:flex items-center space-x-6 text-[11px] text-[#A3A3A3]">
                    <a href="{{ url('/#koleksi') }}" class="hover:text-white transition-colors">Akses Katalog</a>
                    <a href="{{ url('/#publikasi') }}" class="hover:text-white transition-colors">Artikel & Esai</a>
                    <a href="{{ url('/#layanan') }}" class="hover:text-white transition-colors">Layanan Sirkulasi</a>
                    <a href="{{ url('/#tentang') }}" class="hover:text-white transition-colors">Tentang Kami</a>
                </div>
            </div>
        </div>

        <!-- Public Navigation Header -->
        <nav class="h-16 sm:h-20 bg-white/95 backdrop-blur-md border-b border-neutral-border sticky top-0 z-50 flex items-center shadow-xs">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 w-full flex justify-between items-center">
                <!-- Academic Logo & Brand -->
                <a href="{{ url('/') }}" class="flex items-center space-x-2 sm:space-x-3.5 group min-w-0">
                    <img src="{{ asset('images/logo-rpk.png') }}" alt="RPK PUSTAKA IMM SAINTEKMU" class="h-8 sm:h-9 md:h-11 w-auto object-contain shrink-0">
                    <div class="min-w-0">
                        <span class="text-lg sm:text-xl md:text-2xl font-bold tracking-tight text-neutral-dark block leading-none truncate">RPK PUSTAKA</span>
                        <span class="text-[9px] sm:text-[10px] font-semibold text-neutral-muted uppercase tracking-wider block mt-0.5 sm:mt-1 whitespace-nowrap">IMM SAINTEKMU</span>
                    </div>
                </a>
                
                <!-- Center Navigation Links -->
                <div class="hidden lg:flex items-center space-x-8 text-[13px] sm:text-sm font-medium text-neutral-dark">
                    <a href="{{ url('/') }}" class="hover:text-primary transition-colors py-1">Beranda</a>
                    <a href="{{ url('/#koleksi') }}" class="hover:text-primary transition-colors py-1">Koleksi Pilihan</a>
                    <a href="{{ url('/#publikasi') }}" class="hover:text-primary transition-colors py-1">Artikel & Esai</a>
                    <a href="{{ url('/#layanan') }}" class="hover:text-primary transition-colors py-1">Panduan Meminjam</a>
                    <a href="{{ url('/#tentang') }}" class="hover:text-primary transition-colors py-1">Arsip & Visi</a>
                </div>

                <!-- Right Actions -->
                <div class="flex items-center space-x-2 sm:space-x-3 shrink-0">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn-editorial text-[11px] sm:text-xs md:text-sm py-1.5 sm:py-2 px-2.5 sm:px-4 shadow-xs font-semibold flex items-center">
                            <svg class="w-3.5 sm:w-4 h-3.5 sm:h-4 mr-1 sm:mr-1.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>
                            <span class="whitespace-nowrap">{{ Auth::user()->isAdmin() ? 'Admin' : 'Anggota' }}</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-xs sm:text-sm font-medium text-neutral-dark hover:text-primary px-2 sm:px-3 py-1.5 sm:py-2 transition-colors whitespace-nowrap">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" class="btn-editorial text-[11px] sm:text-xs md:text-sm py-1.5 sm:py-2 px-2.5 sm:px-4 shadow-xs font-semibold whitespace-nowrap">
                            Daftar
                        </a>
                    @endauth
                </div>
            </div>
        </nav>

        <!-- Editorial Sub-header (If header slot provided) -->
        @if(isset($header))
            <div class="bg-[#F8F8F7] border-b border-neutral-border py-3 sm:py-4 px-4 sm:px-6">
                <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
                    <h2 class="text-[11px] sm:text-xs sm:text-sm font-bold uppercase tracking-wider text-neutral-dark">
                        {{ $header }}
                    </h2>
                    <nav class="text-[10px] sm:text-[11px] text-neutral-muted font-mono space-x-1 sm:space-x-2">
                        <a href="{{ url('/') }}" class="hover:text-primary transition-colors">Beranda</a>
                        <span>/</span>
                        <span class="text-neutral-dark font-medium">Informasi Pustaka</span>
                    </nav>
                </div>
            </div>
        @endif

        <!-- Main Content Area -->
        <main class="py-6 sm:py-8 md:py-10 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                {{ $slot }}
            </div>
        </main>
    </div>

    <!-- Dignified Institution Footer -->
    <footer id="tentang" class="bg-[#181818] text-[#E5E5E5] pt-10 sm:pt-14 md:pt-16 pb-8 sm:pb-10 md:pb-12 border-t border-[#262626] mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-8 sm:gap-10 md:gap-12 pb-10 sm:pb-12 md:pb-14 border-b border-[#262626]">
                <!-- Brand Column -->
                <div class="md:col-span-5 space-y-3 sm:space-y-4">
                    <div class="flex items-center space-x-2 sm:space-x-3">
                        <img src="{{ asset('images/logo-rpk.png') }}" alt="RPK PUSTAKA IMM SAINTEKMU" class="h-8 sm:h-9 md:h-10 w-auto object-contain bg-white rounded p-0.5 sm:p-1 shrink-0">
                        <div class="min-w-0">
                            <span class="text-lg sm:text-xl font-bold tracking-tight text-white block leading-none whitespace-nowrap">RPK PUSTAKA</span>
                            <span class="text-[9px] sm:text-[10px] uppercase tracking-wider text-[#A3A3A3] font-semibold block mt-0.5 sm:mt-1 whitespace-nowrap">IMM SAINTEKMU</span>
                        </div>
                    </div>
                    <p class="text-[11px] sm:text-xs md:text-[13px] text-[#A3A3A3] leading-relaxed max-w-sm pt-1 sm:pt-2">
                        Lembaga perpustakaan riset dan dokumentasi ilmiah. Didedikasikan untuk memelihara warisan pemikiran dan menyediakan akses setara terhadap ilmu pengetahuan.
                    </p>
                    <div class="flex items-center gap-1.5 sm:gap-2 pt-1 sm:pt-2">
                        <svg class="w-3 sm:w-3.5 h-3 sm:h-3.5 text-accent fill-current shrink-0" viewBox="0 0 24 24">
                            <path d="M12 2l2.4 7.2h7.6l-6.2 4.5 2.4 7.3-6.2-4.6-6.2 4.6 2.4-7.3-6.2-4.5h7.6z"/>
                        </svg>
                        <p class="italic text-[11px] sm:text-xs md:text-[13px] text-white font-medium">
                            Mencerahkan Generasi Melalui Akses Pengetahuan
                        </p>
                    </div>
                </div>

                <!-- Navigation Links -->
                <div class="md:col-span-3 space-y-2.5 sm:space-y-3">
                    <h4 class="text-[11px] sm:text-xs md:text-[13px] font-semibold uppercase tracking-wider text-white">Navigasi Utama</h4>
                    <ul class="space-y-2 sm:space-y-2.5 text-[11px] sm:text-xs md:text-[13px] text-[#A3A3A3]">
                        <li><a href="{{ url('/') }}" class="hover:text-white transition-colors">Beranda Utama</a></li>
                        <li><a href="{{ url('/#koleksi') }}" class="hover:text-white transition-colors">Katalog Koleksi</a></li>
                        <li><a href="{{ url('/#publikasi') }}" class="hover:text-white transition-colors">Artikel & Esai</a></li>
                        @auth
                            <li><a href="{{ url('/dashboard') }}" class="hover:text-white transition-colors">{{ Auth::user()->isAdmin() ? 'Dasbor Admin' : 'Dasbor Anggota' }}</a></li>
                        @else
                            <li><a href="{{ route('login') }}" class="hover:text-white transition-colors">Masuk ke Portal</a></li>
                            <li><a href="{{ route('register') }}" class="hover:text-white transition-colors">Pendaftaran Anggota</a></li>
                        @endauth
                    </ul>
                </div>

                <!-- Operational Hours -->
                <div class="md:col-span-4 space-y-2 sm:space-y-2.5 md:space-y-3">
                    <h4 class="text-[11px] sm:text-xs md:text-[13px] font-semibold uppercase tracking-wider text-white">Jam Layanan Sirkulasi</h4>
                    <div class="space-y-1.5 sm:space-y-2 text-[11px] sm:text-xs md:text-[13px] text-[#A3A3A3]">
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-0.5 sm:gap-2 border-b border-[#262626] pb-1.5 sm:pb-1.5">
                            <span class="whitespace-nowrap">Senin s.d. Jumat:</span>
                            <span class="text-white font-medium whitespace-nowrap">06:00 – 00:00 WIB</span>
                        </div>
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-0.5 sm:gap-2 pt-1">
                            <span class="whitespace-nowrap">Sabtu, Minggu & Libur:</span>
                            <span class="text-[#737373] italic leading-snug">Tutup (Layanan Digital 24 Jam)</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Copyright Bar -->
            <div class="pt-8 flex flex-col sm:flex-row items-center justify-between text-xs text-[#888888] gap-4">
                <p>&copy; {{ date('Y') }} RPK PUSTAKA IMM SAINTEKMU, Hak Cipta Dilindungi.</p>
                <p class="font-medium">IMM SAINTEKMU</p>
            </div>
        </div>
    </footer>
</body>
</html>
