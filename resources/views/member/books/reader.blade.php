<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Membaca: {{ $book->title }} — RPK PUSTAKA IMM SAINTEK MU Digital Reader</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('images/logo-rpk.ico') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/logo-rpk.ico') }}">

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: #121212;
            color: #E5E5E5;
        }
        /* Hide scrollbars on full page reader wrapper */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>

<body class="h-full w-full overflow-hidden flex flex-col bg-[#121212] select-none text-neutral-200">
    
    <!-- Top Reader Navigation Bar (Editorial Dark Masthead) -->
    <header id="reader-header" class="h-14 bg-[#181818] border-b border-neutral-800 flex items-center justify-between px-4 sm:px-6 z-50 shrink-0 transition-all duration-300 shadow-md">
        
        <!-- Left: Back / Exit & Book Info -->
        <div class="flex items-center space-x-3 sm:space-x-4 min-w-0">
            <a href="{{ $backUrl ?? route('dashboard') }}" 
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded bg-neutral-800 hover:bg-neutral-700 text-xs font-semibold text-neutral-200 hover:text-white transition-colors border border-neutral-700 shadow-xs"
               title="Keluar dari Pembaca Digital">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                <span class="hidden sm:inline">Tutup</span>
            </a>

            <div class="h-5 w-px bg-neutral-700 hidden sm:block"></div>

            <div class="min-w-0">
                <h1 class="text-xs sm:text-sm font-bold text-white tracking-tight truncate max-w-[220px] sm:max-w-md md:max-w-lg" title="{{ $book->title }}">
                    {{ $book->title }}
                </h1>
                <p class="text-[11px] text-neutral-400 truncate hidden xs:block">
                    <span>{{ $book->author }}</span>
                    @if($book->isbn)
                        <span class="text-neutral-500 font-mono text-[10px]"> • ISBN: {{ $book->isbn }}</span>
                    @endif
                </p>
            </div>
        </div>

        <!-- Center / Right: Status Badge & Fullscreen Controls -->
        <div class="flex items-center space-x-2 sm:space-x-3 shrink-0">
            @if($activeLoan)
                <div class="hidden md:flex items-center gap-1.5 bg-primary/20 border border-primary/40 px-3 py-1 rounded text-xs text-red-200 font-medium">
                    <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                    <span>Tenggat: <strong>{{ \Carbon\Carbon::parse($activeLoan->due_date)->translatedFormat('d M Y') }}</strong></span>
                </div>
            @else
                <div class="hidden md:flex items-center gap-1.5 bg-[#FFF9ED]/10 border border-[#FDE68A]/30 px-3 py-1 rounded text-xs text-amber-300 font-medium">
                    <svg class="w-3.5 h-3.5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    <span>Mode Kuratorial Admin</span>
                </div>
            @endif

            <!-- Fullscreen View Toggle Button -->
            <button type="button" 
                    id="fullscreen-toggle-btn"
                    onclick="toggleFullScreen()" 
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-[#C62828] hover:bg-[#A71D1D] text-white rounded text-xs font-semibold uppercase tracking-wider transition-colors shadow-xs cursor-pointer"
                    title="Beralih ke Layar Penuh (Fullscreen)">
                <svg id="fullscreen-expand-icon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5"></path>
                </svg>
                <svg id="fullscreen-compress-icon" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                <span id="fullscreen-btn-text" class="hidden sm:inline">Layar Penuh</span>
            </button>
        </div>
    </header>

    <!-- Full Page Immersive Digital Reader Frame -->
    <main id="reader-container" class="flex-1 w-full h-full bg-[#242424] relative overflow-hidden flex flex-col">
        <iframe 
            id="digital-reader-frame"
            src="{{ $streamUrl }}#toolbar=1&navpanes=1&scrollbar=1&view=FitH" 
            class="w-full h-full border-0 flex-1 bg-[#242424]"
            title="Dokumen Digital: {{ $book->title }}"
            allow="fullscreen; autoplay"
        ></iframe>
    </main>

    <!-- Client-side reader protection and Fullscreen handling script -->
    <script>
        function toggleFullScreen() {
            const expandIcon = document.getElementById('fullscreen-expand-icon');
            const compressIcon = document.getElementById('fullscreen-compress-icon');
            const btnText = document.getElementById('fullscreen-btn-text');

            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().then(() => {
                    if (expandIcon) expandIcon.classList.add('hidden');
                    if (compressIcon) compressIcon.classList.remove('hidden');
                    if (btnText) btnText.innerText = 'Keluar Layar Penuh';
                }).catch(err => {
                    console.log(`Error attempting to enable full-screen mode: ${err.message}`);
                });
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen().then(() => {
                        if (expandIcon) expandIcon.classList.remove('hidden');
                        if (compressIcon) compressIcon.classList.add('hidden');
                        if (btnText) btnText.innerText = 'Layar Penuh';
                    });
                }
            }
        }

        document.addEventListener('fullscreenchange', () => {
            const expandIcon = document.getElementById('fullscreen-expand-icon');
            const compressIcon = document.getElementById('fullscreen-compress-icon');
            const btnText = document.getElementById('fullscreen-btn-text');

            if (!document.fullscreenElement) {
                if (expandIcon) expandIcon.classList.remove('hidden');
                if (compressIcon) compressIcon.classList.add('hidden');
                if (btnText) btnText.innerText = 'Layar Penuh';
            } else {
                if (expandIcon) expandIcon.classList.add('hidden');
                if (compressIcon) compressIcon.classList.remove('hidden');
                if (btnText) btnText.innerText = 'Keluar Layar Penuh';
            }
        });

        // Reader copyright protection (Prevent right click / save shortcut)
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });

        document.addEventListener('keydown', function(e) {
            // Prevent Ctrl+S, Ctrl+P, Ctrl+U
            if ((e.ctrlKey || e.metaKey) && (e.key === 's' || e.key === 'p' || e.key === 'u')) {
                e.preventDefault();
            }
            // 'f' shortcut for fullscreen
            if (e.key === 'f' && !e.ctrlKey && !e.metaKey && !e.altKey && document.activeElement.tagName !== 'INPUT') {
                toggleFullScreen();
            }
        });
    </script>
</body>
</html>
