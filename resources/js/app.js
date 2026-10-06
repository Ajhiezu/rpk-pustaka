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

/**
 * Render page 1 of a PDF file to a WebP/JPEG Blob and base64 image using PDF.js.
 * @param {File} file
 * @param {number} timeoutMs
 * @returns {Promise<{success: boolean, blob?: Blob, imageBase64?: string, pageCount?: number, error?: string, isEncrypted?: boolean}>}
 */
window.renderPdfFirstPage = async function(file, timeoutMs = 12000) {
    if (!file || (file.type !== 'application/pdf' && !file.name.toLowerCase().endsWith('.pdf'))) {
        return { success: false, error: 'Bukan berkas dokumen PDF valid.' };
    }

    // Wrap execution with a strict timeout to guarantee it never hangs
    const renderPromise = (async () => {
        try {
            const arrayBuffer = await file.arrayBuffer();
            const loadingTask = pdfjsLib.getDocument({
                data: arrayBuffer,
                stopAtErrors: false,
                disableFontFace: false,
                nativeImageDecoderSupport: 'none',
            });

            loadingTask.onPassword = function(callback, reason) {
                callback(''); // abort on password prompt
            };

            const pdf = await loadingTask.promise;
            const page = await pdf.getPage(1);

            // Scale 1.5 for crisp cover image
            const viewport = page.getViewport({ scale: 1.5 });
            const canvas = document.createElement('canvas');
            const context = canvas.getContext('2d', { alpha: false });
            
            canvas.width = viewport.width;
            canvas.height = viewport.height;

            // Fill solid white background
            context.fillStyle = '#FFFFFF';
            context.fillRect(0, 0, canvas.width, canvas.height);

            await page.render({
                canvasContext: context,
                viewport: viewport,
            }).promise;

            // Generate Blob (WebP with JPEG fallback)
            let blob = await new Promise(resolve => {
                try {
                    canvas.toBlob(resolve, 'image/webp', 0.85);
                } catch (e) {
                    resolve(null);
                }
            });

            if (!blob) {
                blob = await new Promise(resolve => {
                    canvas.toBlob(resolve, 'image/jpeg', 0.85);
                });
            }

            let dataUrl;
            try {
                dataUrl = canvas.toDataURL('image/webp', 0.85);
            } catch (e) {
                dataUrl = canvas.toDataURL('image/jpeg', 0.85);
            }

            return {
                success: true,
                blob: blob,
                imageBase64: dataUrl,
                pageCount: pdf.numPages,
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

        const isHidden = input.type === 'password';
        input.type = isHidden ? 'text' : 'password';

        const eyeIcon = btn.querySelector('.password-toggle-icon-eye');
        const eyeOffIcon = btn.querySelector('.password-toggle-icon-eye-off');
        if (eyeIcon && eyeOffIcon) {
            eyeIcon.classList.toggle('hidden', !isHidden);
            eyeOffIcon.classList.toggle('hidden', isHidden);
        }

        btn.setAttribute('aria-label', isHidden ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
    });
});

window.Alpine = Alpine;
Alpine.start();
