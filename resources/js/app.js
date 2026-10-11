import './bootstrap';

import Alpine from 'alpinejs';
import * as pdfjsLib from 'pdfjs-dist';

// Configure PDF.js worker reliably via Blob URL from CDN to eliminate Vite cross-origin port issues
const WORKER_CDN_URL = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
let workerBlobUrl = null;

async function ensureWorkerSrc() {
    if (pdfjsLib.GlobalWorkerOptions.workerSrc) {
        return;
    }
    try {
        const res = await fetch(WORKER_CDN_URL);
        if (res.ok) {
            const text = await res.text();
            const blob = new Blob([text], { type: 'text/javascript' });
            workerBlobUrl = URL.createObjectURL(blob);
            pdfjsLib.GlobalWorkerOptions.workerSrc = workerBlobUrl;
            return;
        }
    } catch (e) {
        // Fallback directly to CDN URL if fetch fails
    }
    pdfjsLib.GlobalWorkerOptions.workerSrc = WORKER_CDN_URL;
}

ensureWorkerSrc();
window.pdfjsLib = pdfjsLib;

/**
 * Render page 1 of a PDF file to a WebP/JPEG Blob and base64 image using PDF.js.
 * @param {File} file
 * @param {number} timeoutMs
 * @returns {Promise<{success: boolean, blob?: Blob, imageBase64?: string, pageCount?: number, error?: string, isEncrypted?: boolean}>}
 */
window.renderPdfFirstPage = async function(file, timeoutMs = 8000) {
    if (!file || (file.type !== 'application/pdf' && !file.name.toLowerCase().endsWith('.pdf'))) {
        return { success: false, error: 'Bukan berkas dokumen PDF valid.' };
    }

    let objectUrl = null;
    const renderPromise = (async () => {
        try {
            // Gunakan Object URL agar browser engine membaca stream langsung tanpa duplikasi RAM
            objectUrl = URL.createObjectURL(file);

            const loadingTask = pdfjsLib.getDocument({
                url: objectUrl,
                stopAtErrors: false,
                disableFontFace: false,
                nativeImageDecoderSupport: 'none',
            });

            loadingTask.onPassword = function(callback) {
                callback(''); // abort on password prompt
            };

            const pdf = await loadingTask.promise;
            const pageCount = pdf.numPages || 1;

            // Ekstraksi metadata PDF (Penulis, Tahun, Judul) secara instan dari dokumen info
            let author = null;
            let year = null;
            let title = null;

            try {
                const meta = await pdf.getMetadata();
                if (meta && meta.info) {
                    if (meta.info.Author && typeof meta.info.Author === 'string') {
                        const a = meta.info.Author.trim();
                        if (a.length > 1 && !/^(administrator|user|admin|microsoft|wps|canva)/i.test(a)) {
                            author = a;
                        }
                    }
                    if (meta.info.Title && typeof meta.info.Title === 'string') {
                        const t = meta.info.Title.trim();
                        if (t.length > 2 && !/^(microsoft word|untitled|wps office|canva|adobe|print)/i.test(t)) {
                            title = t;
                        }
                    }
                    if (meta.info.CreationDate) {
                        const match = String(meta.info.CreationDate).match(/(?:D:)?(\d{4})/);
                        if (match) {
                            const y = parseInt(match[1]);
                            if (y >= 1800 && y <= new Date().getFullYear() + 1) {
                                year = y;
                            }
                        }
                    }
                }
            } catch (metaErr) {
                // Ignore metadata error
            }

            // Render Halaman 1 sebagai Cover Thumbnail (Maks lebar 320px agar super cepat & ringan)
            const page = await pdf.getPage(1);
            const baseViewport = page.getViewport({ scale: 1.0 });
            const targetWidth = 320;
            const scale = Math.min(1.0, targetWidth / Math.max(1, baseViewport.width));
            const viewport = page.getViewport({ scale: scale });

            const canvas = document.createElement('canvas');
            const context = canvas.getContext('2d', { alpha: false });
            
            canvas.width = Math.round(viewport.width);
            canvas.height = Math.round(viewport.height);

            // Background putih solid
            context.fillStyle = '#FFFFFF';
            context.fillRect(0, 0, canvas.width, canvas.height);

            await page.render({
                canvasContext: context,
                viewport: viewport,
            }).promise;

            // Generate Blob WebP (ringan ~15-25KB, kualitas optimal)
            let blob = await new Promise(resolve => {
                try {
                    canvas.toBlob(resolve, 'image/webp', 0.80);
                } catch (e) {
                    resolve(null);
                }
            });

            if (!blob) {
                blob = await new Promise(resolve => {
                    canvas.toBlob(resolve, 'image/jpeg', 0.80);
                });
            }

            // Preview URL instan tanpa string Base64 jutaan karakter
            let previewUrl = blob ? URL.createObjectURL(blob) : null;
            let dataUrl = null;
            try {
                dataUrl = canvas.toDataURL('image/webp', 0.80);
            } catch (e) {
                try {
                    dataUrl = canvas.toDataURL('image/jpeg', 0.80);
                } catch (e2) {}
            }

            return {
                success: true,
                blob: blob,
                previewUrl: previewUrl,
                imageBase64: dataUrl,
                pageCount: pageCount,
                author: author,
                year: year,
                title: title,
            };
        } catch (err) {
            console.error('PDF.js render error:', err);
            const errorMsg = err?.message || String(err);
            const isEncrypted = errorMsg.toLowerCase().includes('password') || errorMsg.toLowerCase().includes('encrypted');
            return {
                success: false,
                error: isEncrypted ? 'Berkas PDF terenkripsi / berpassword.' : errorMsg,
                isEncrypted: isEncrypted,
            };
        } finally {
            if (objectUrl) {
                try { URL.revokeObjectURL(objectUrl); } catch (e) {}
            }
        }
    })();

    const timeoutPromise = new Promise(resolve => {
        setTimeout(() => {
            resolve({
                success: false,
                error: 'Rendering timeout (sampul otomatis dilewati).',
                isTimeout: true,
            });
        }, timeoutMs);
    });

    return Promise.race([renderPromise, timeoutPromise]);
};

document.addEventListener('DOMContentLoaded', function () {
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('[data-password-toggle]');
        if (!btn) return;

        e.preventDefault();
        const targetId = btn.getAttribute('data-target');
        if (!targetId) return;

        const input = document.getElementById(targetId);
        if (!input) return;

        const isCurrentlyPassword = input.type === 'password';
        input.type = isCurrentlyPassword ? 'text' : 'password';

        const eyeIcon = btn.querySelector('.password-toggle-icon-eye');
        const eyeOffIcon = btn.querySelector('.password-toggle-icon-eye-off');
        if (eyeIcon && eyeOffIcon) {
            const isTextVisible = input.type === 'text';
            eyeIcon.classList.toggle('hidden', !isTextVisible);
            eyeOffIcon.classList.toggle('hidden', isTextVisible);
        }

        btn.setAttribute('aria-label', input.type === 'text' ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
    });
});

window.Alpine = Alpine;
Alpine.start();
