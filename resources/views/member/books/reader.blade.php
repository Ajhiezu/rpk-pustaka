<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Membaca: {{ $book->title }} — RPK PUSTAKA IMM SAINTEKMU Digital Reader</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('images/logo-rpk.ico') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/logo-rpk.ico') }}">

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- PDF.js CDN Fallback -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: #121212;
            color: #E5E5E5;
        }
        .reader-canvas-container {
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.5), 0 8px 10px -6px rgba(0, 0, 0, 0.5);
        }
        /* Custom scrollbar for reader container */
        .reader-scroll::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        .reader-scroll::-webkit-scrollbar-track {
            background: #1a1a1a;
        }
        .reader-scroll::-webkit-scrollbar-thumb {
            background: #333333;
            border-radius: 4px;
        }
        .reader-scroll::-webkit-scrollbar-thumb:hover {
            background: #444444;
        }
    </style>
</head>

<body class="h-full w-full overflow-hidden flex flex-col bg-[#121212] select-none text-neutral-200">
    
    <!-- Top Reader Navigation Bar (Editorial Dark Masthead) -->
    <header id="reader-header" class="h-14 bg-[#181818] border-b border-neutral-800 flex items-center justify-between px-3 sm:px-6 z-50 shrink-0 shadow-md">
        
        <!-- Left: Back / Exit & Book Info -->
        <div class="flex items-center space-x-2 sm:space-x-3 min-w-0">
            <a href="{{ $backUrl ?? route('dashboard') }}" 
               class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded bg-neutral-800 hover:bg-neutral-700 text-xs font-semibold text-neutral-200 hover:text-white transition-colors border border-neutral-700 shadow-xs shrink-0"
               title="Keluar dari Pembaca Digital">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                <span class="hidden sm:inline">Tutup</span>
            </a>

            <div class="h-4 w-px bg-neutral-700 hidden xs:block"></div>

            <div class="min-w-0">
                <h1 class="text-xs sm:text-sm font-bold text-white tracking-tight truncate max-w-[140px] xs:max-w-[200px] sm:max-w-xs md:max-w-md" title="{{ $book->title }}">
                    {{ $book->title }}
                </h1>
                <p class="text-[10px] sm:text-[11px] text-neutral-400 truncate hidden xs:block">
                    <span>{{ $book->author }}</span>
                </p>
            </div>
        </div>

        <!-- Center: Page Navigation (Quick Jump) -->
        <div class="flex items-center space-x-1 sm:space-x-2 bg-neutral-900/90 border border-neutral-800 px-2 py-1 rounded-md shadow-xs">
            <button type="button" id="prev-page-btn" onclick="goToPrevPage()" 
                    class="p-1 rounded hover:bg-neutral-800 text-neutral-300 hover:text-white disabled:opacity-30 disabled:hover:bg-transparent transition-colors cursor-pointer" 
                    title="Halaman Sebelumnya (Panah Kiri)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </button>

            <div class="flex items-center text-xs font-mono font-medium text-neutral-300 px-1">
                <input type="number" id="page-num-input" min="1" value="1" 
                       class="w-10 sm:w-12 bg-neutral-800 border border-neutral-700 text-center text-xs text-white py-0.5 px-1 rounded focus:outline-none focus:border-primary">
                <span class="mx-1 text-neutral-500">/</span>
                <span id="page-count-display">--</span>
            </div>

            <button type="button" id="next-page-btn" onclick="goToNextPage()" 
                    class="p-1 rounded hover:bg-neutral-800 text-neutral-300 hover:text-white disabled:opacity-30 disabled:hover:bg-transparent transition-colors cursor-pointer" 
                    title="Halaman Selanjutnya (Panah Kanan)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </button>
        </div>

        <!-- Right: Zoom & Fullscreen Controls -->
        <div class="flex items-center space-x-1.5 sm:space-x-2 shrink-0">
            <!-- Zoom & Fit Controls (Desktop & Tablet) -->
            <div class="hidden sm:flex items-center bg-neutral-900/90 border border-neutral-800 rounded-md p-0.5">
                <button type="button" onclick="zoomOut()" class="p-1 rounded hover:bg-neutral-800 text-neutral-300 hover:text-white transition-colors cursor-pointer" title="Perkecil (-)">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                </button>
                <button type="button" onclick="toggleFitMode()" id="zoom-level-btn" class="px-2 py-0.5 text-[11px] font-mono text-neutral-300 hover:text-white cursor-pointer" title="Ubah Mode: Fit Halaman / Fit Lebar">
                    Fit Halaman
                </button>
                <button type="button" onclick="zoomIn()" class="p-1 rounded hover:bg-neutral-800 text-neutral-300 hover:text-white transition-colors cursor-pointer" title="Perbesar (+)">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                </button>
            </div>

            <!-- Fullscreen View Toggle Button -->
            <button type="button" 
                    id="fullscreen-toggle-btn"
                    onclick="toggleFullScreen()" 
                    class="inline-flex items-center gap-1 p-1.5 sm:px-3 sm:py-1.5 bg-primary hover:bg-primary-dark text-white rounded text-xs font-semibold uppercase tracking-wider transition-colors shadow-xs cursor-pointer"
                    title="Beralih ke Layar Penuh">
                <svg id="fullscreen-expand-icon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5"></path>
                </svg>
                <svg id="fullscreen-compress-icon" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                <span id="fullscreen-btn-text" class="hidden md:inline">Layar Penuh</span>
            </button>
        </div>
    </header>

    <!-- Main Canvas View Area -->
    <main id="reader-viewport" class="flex-1 w-full h-full bg-[#1e1e1e] relative overflow-auto reader-scroll flex flex-col items-center p-2 sm:p-4">
        
        <!-- Loading State Indicator -->
        <div id="loading-indicator" class="absolute inset-0 z-30 bg-[#121212]/90 flex flex-col items-center justify-center p-6 space-y-4">
            <div class="relative w-12 h-12">
                <div class="w-12 h-12 rounded-full border-2 border-neutral-700 border-t-primary animate-spin"></div>
            </div>
            <div class="text-center space-y-1">
                <p class="text-sm font-semibold text-white">Memuat Naskah Digital...</p>
                <p id="loading-progress" class="text-xs text-neutral-400 font-mono">Menghubungkan ke server perpustakaan</p>
            </div>
        </div>

        <!-- Error State Indicator -->
        <div id="error-indicator" class="hidden absolute inset-0 z-30 bg-[#121212] flex flex-col items-center justify-center p-6 text-center space-y-4">
            <div class="w-14 h-14 rounded-full bg-red-900/30 border border-red-800 text-danger flex items-center justify-center">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <div class="max-w-md space-y-2">
                <h3 class="text-base font-bold text-white">Gagal Membuka Naskah PDF</h3>
                <p id="error-message" class="text-xs text-neutral-400 leading-relaxed">Terjadi kendala saat membaca berkas digital buku.</p>
            </div>
            <div class="pt-2 flex gap-3">
                <button type="button" onclick="location.reload()" class="btn-editorial text-xs py-2 px-4 shadow-xs">
                    Coba Muat Ulang
                </button>
                <a href="{{ $backUrl ?? route('dashboard') }}" class="btn-editorial-outline text-xs py-2 px-4">
                    Kembali
                </a>
            </div>
        </div>

        <!-- Canvas Container Wrapper -->
        <div id="canvas-wrapper" class="relative my-auto flex flex-col items-center justify-center min-h-full">
            <canvas id="pdf-render-canvas" class="reader-canvas-container rounded bg-white shadow-2xl transition-transform duration-150"></canvas>
        </div>
    </main>

    <!-- Bottom Mobile Toolbar (Quick navigation & zoom for phones) -->
    <footer class="sm:hidden h-12 bg-[#181818] border-t border-neutral-800 flex items-center justify-between px-4 z-40 shrink-0">
        <button type="button" onclick="goToPrevPage()" class="flex items-center gap-1 px-3 py-1 bg-neutral-800 hover:bg-neutral-700 text-white rounded text-xs font-semibold">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            <span>Sebelumnya</span>
        </button>

        <div class="flex items-center gap-2">
            <button type="button" onclick="zoomOut()" class="p-1.5 bg-neutral-800 text-neutral-300 rounded" title="Perkecil">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
            </button>
            <button type="button" onclick="toggleFitMode()" class="px-2 py-1 bg-neutral-800 hover:bg-neutral-700 text-neutral-200 font-mono text-[10px] rounded cursor-pointer" title="Ubah Mode Tampilan">
                Fit
            </button>
            <button type="button" onclick="zoomIn()" class="p-1.5 bg-neutral-800 text-neutral-300 rounded" title="Perbesar">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            </button>
        </div>

        <button type="button" onclick="goToNextPage()" class="flex items-center gap-1 px-3 py-1 bg-neutral-800 hover:bg-neutral-700 text-white rounded text-xs font-semibold">
            <span>Berikutnya</span>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </button>
    </footer>

    <!-- PDF.js Reader Logic -->
    <script>
        const pdfUrl = @json($streamUrl);
        let pdfDoc = null;
        let pageNum = 1;
        let pageRendering = false;
        let pageNumPending = null;
        let scale = 1.0;
        let fitMode = 'page'; // 'page' (Fit Halaman Asli / Proporsional), 'width' (Fit Lebar), or 'manual'

        const canvas = document.getElementById('pdf-render-canvas');
        const ctx = canvas.getContext('2d', { alpha: false });
        const viewportContainer = document.getElementById('reader-viewport');
        const pageNumInput = document.getElementById('page-num-input');
        const pageCountDisplay = document.getElementById('page-count-display');
        const loadingIndicator = document.getElementById('loading-indicator');
        const loadingProgress = document.getElementById('loading-progress');
        const errorIndicator = document.getElementById('error-indicator');
        const errorMessage = document.getElementById('error-message');
        const zoomLevelBtn = document.getElementById('zoom-level-btn');

        async function initPdf() {
            try {
                // Ensure PDF.js is ready
                const pdfjs = window.pdfjsLib || (typeof pdfjsLib !== 'undefined' ? pdfjsLib : null);
                if (!pdfjs) {
                    throw new Error('Pustaka PDF.js belum siap. Silakan muat ulang halaman.');
                }

                if (!pdfjs.GlobalWorkerOptions.workerSrc) {
                    pdfjs.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
                }

                loadingProgress.innerText = 'Mengunduh data naskah...';

                const loadingTask = pdfjs.getDocument({
                    url: pdfUrl,
                    withCredentials: true,
                    cMapUrl: 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/cmaps/',
                    cMapPacked: true,
                });

                loadingTask.onProgress = function(progressData) {
                    if (progressData.total > 0) {
                        const percent = Math.min(100, Math.round((progressData.loaded / progressData.total) * 100));
                        loadingProgress.innerText = 'Memuat naskah: ' + percent + '%';
                    }
                };

                pdfDoc = await loadingTask.promise;
                pageCountDisplay.innerText = pdfDoc.numPages;
                pageNumInput.max = pdfDoc.numPages;

                loadingIndicator.classList.add('hidden');
                
                // Initial render respecting native PDF aspect ratio & orientation
                renderPage(pageNum);
            } catch (err) {
                console.error('PDF Reader Init Error:', err);
                loadingIndicator.classList.add('hidden');
                errorIndicator.classList.remove('hidden');
                errorMessage.innerText = err.message || 'Gagal memuat dokumen PDF naskah.';
            }
        }

        async function renderPage(num) {
            if (!pdfDoc) return;
            pageRendering = true;

            try {
                const page = await pdfDoc.getPage(num);
                
                // Read natural unscaled dimensions of this specific PDF page
                const unscaledViewport = page.getViewport({ scale: 1.0 });
                const paddingHoriz = window.innerWidth < 640 ? 16 : 48;
                const paddingVert = window.innerWidth < 640 ? 16 : 48;
                const availableWidth = Math.max(300, viewportContainer.clientWidth - paddingHoriz);
                const availableHeight = Math.max(300, viewportContainer.clientHeight - paddingVert);
                
                let targetScale = scale;
                if (fitMode === 'page') {
                    // Fit entire page inside the viewport according to its natural aspect ratio (Portrait or Landscape)
                    const scaleWidth = availableWidth / unscaledViewport.width;
                    const scaleHeight = availableHeight / unscaledViewport.height;
                    targetScale = Math.min(scaleWidth, scaleHeight);
                    scale = targetScale;
                    if (zoomLevelBtn) zoomLevelBtn.innerText = 'Fit Halaman';
                } else if (fitMode === 'width') {
                    targetScale = availableWidth / unscaledViewport.width;
                    scale = targetScale;
                    if (zoomLevelBtn) zoomLevelBtn.innerText = 'Fit Lebar';
                } else {
                    if (zoomLevelBtn) zoomLevelBtn.innerText = Math.round(scale * 100) + '%';
                }

                // Support HiDPI / Retina displays without distortion
                const outputScale = window.devicePixelRatio || 1;
                const viewport = page.getViewport({ scale: targetScale });

                canvas.width = Math.floor(viewport.width * outputScale);
                canvas.height = Math.floor(viewport.height * outputScale);
                canvas.style.width = Math.floor(viewport.width) + 'px';
                canvas.style.height = Math.floor(viewport.height) + 'px';

                const transform = outputScale !== 1 
                    ? [outputScale, 0, 0, outputScale, 0, 0] 
                    : null;

                const renderContext = {
                    canvasContext: ctx,
                    transform: transform,
                    viewport: viewport,
                };

                await page.render(renderContext).promise;

                pageRendering = false;
                if (pageNumPending !== null) {
                    renderPage(pageNumPending);
                    pageNumPending = null;
                }

                // Update controls
                pageNumInput.value = num;
                document.getElementById('prev-page-btn').disabled = (num <= 1);
                document.getElementById('next-page-btn').disabled = (num >= pdfDoc.numPages);
            } catch (renderErr) {
                console.error('Page render error:', renderErr);
                pageRendering = false;
            }
        }

        function queueRenderPage(num) {
            if (pageRendering) {
                pageNumPending = num;
            } else {
                renderPage(num);
            }
        }

        function goToPrevPage() {
            if (pageNum <= 1) return;
            pageNum--;
            queueRenderPage(pageNum);
            viewportContainer.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function goToNextPage() {
            if (!pdfDoc || pageNum >= pdfDoc.numPages) return;
            pageNum++;
            queueRenderPage(pageNum);
            viewportContainer.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function zoomIn() {
            fitMode = 'manual';
            scale = Math.min(3.0, scale + 0.2);
            queueRenderPage(pageNum);
        }

        function zoomOut() {
            fitMode = 'manual';
            scale = Math.max(0.4, scale - 0.2);
            queueRenderPage(pageNum);
        }

        function toggleFitMode() {
            if (fitMode === 'page') {
                fitMode = 'width';
            } else {
                fitMode = 'page';
            }
            queueRenderPage(pageNum);
        }

        function fitWidth() {
            fitMode = 'width';
            queueRenderPage(pageNum);
        }

        function fitPage() {
            fitMode = 'page';
            queueRenderPage(pageNum);
        }

        // Direct page input listener
        pageNumInput.addEventListener('change', function() {
            let val = parseInt(this.value, 10);
            if (!isNaN(val) && pdfDoc) {
                val = Math.max(1, Math.min(pdfDoc.numPages, val));
                pageNum = val;
                queueRenderPage(pageNum);
            }
        });

        // Window resize handler (Re-fit width on orientation change / window resize)
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => {
                if (fitMode === 'width') {
                    renderPage(pageNum);
                }
            }, 200);
        });

        // Fullscreen Toggle
        function toggleFullScreen() {
            const expandIcon = document.getElementById('fullscreen-expand-icon');
            const compressIcon = document.getElementById('fullscreen-compress-icon');
            const btnText = document.getElementById('fullscreen-btn-text');

            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().then(() => {
                    if (expandIcon) expandIcon.classList.add('hidden');
                    if (compressIcon) compressIcon.classList.remove('hidden');
                    if (btnText) btnText.innerText = 'Keluar';
                }).catch(err => {
                    console.log(`Error attempting to enable fullscreen: ${err.message}`);
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
                if (btnText) btnText.innerText = 'Keluar';
            }
        });

        // Keyboard Shortcuts (Arrow Left/Right, +, -, f)
        document.addEventListener('keydown', function(e) {
            if (e.target.tagName === 'INPUT') return;
            if (e.key === 'ArrowLeft') {
                goToPrevPage();
            } else if (e.key === 'ArrowRight') {
                goToNextPage();
            } else if (e.key === '+' || e.key === '=') {
                zoomIn();
            } else if (e.key === '-') {
                zoomOut();
            } else if (e.key.toLowerCase() === 'f' && !e.ctrlKey && !e.metaKey) {
                toggleFullScreen();
            }
        });

        // Touch Swipe Navigation for mobile screens
        let touchStartX = 0;
        let touchStartY = 0;
        viewportContainer.addEventListener('touchstart', function(e) {
            if (e.touches.length === 1) {
                touchStartX = e.touches[0].clientX;
                touchStartY = e.touches[0].clientY;
            }
        }, { passive: true });

        viewportContainer.addEventListener('touchend', function(e) {
            if (e.changedTouches.length === 1) {
                const diffX = e.changedTouches[0].clientX - touchStartX;
                const diffY = e.changedTouches[0].clientY - touchStartY;
                
                // Only trigger if horizontal swipe is dominant and significant
                if (Math.abs(diffX) > 60 && Math.abs(diffY) < 50) {
                    if (diffX < 0) {
                        goToNextPage(); // Swiped left -> Next
                    } else {
                        goToPrevPage(); // Swiped right -> Prev
                    }
                }
            }
        }, { passive: true });

        // Copyright Protection (disable context menu)
        document.addEventListener('contextmenu', e => e.preventDefault());

        // Launch reader on DOM ready
        document.addEventListener('DOMContentLoaded', initPdf);
    </script>
</body>
</html>
