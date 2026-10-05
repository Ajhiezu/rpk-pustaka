# UI Design System & Brand Identity Rules — RPK Pustaka

Dokumen aturan ini otomatis dimuat setiap kali ada sesi kerja di proyek **tech-perpus**.

## 1. Aturan Warna Wajib
- **Primary:** RPK RED (`#C62828`, hover: `#A71D1D`, light tint: `#FEF2F2`)
- **Secondary / Accent:** RPK GOLD (`#E5A11A`, light tint: `#FFF9ED`, border: `#FDE68A`)
- **Background Utama:** WHITE (`#FFFFFF`) — Rasio 70-80%
- **Surface Alternatif:** SOFT NEUTRAL (`#F8F8F7`)
- **Teks:** DARK (`#181818`), BODY (`#666666`), MUTED (`#888888`), BORDER (`#E5E5E5`)
- **DILARANG:** Menggunakan warna cokelat (*brown*), krem (*beige*), atau terakota (*terracotta*). Dilarang memakai gradient merah-kuning.

## 2. Tipografi
- **Headings / Judul:** `Playfair Display` (`font-serif`)
- **Body & UI Controls:** `Plus Jakarta Sans` (`font-sans`)

## 3. Aset Logo & Icon
- Logo Asli: `public/images/logo-rpk.png`
- Favicon: `public/images/logo-rpk.ico` dan `public/favicon.ico`
- Komponen Logo: `<x-application-logo>` di `resources/views/components/application-logo.blade.php`

## 4. Komponen Kunci
- Tombol Utama: `.btn-editorial` (Merah `#C62828`, teks putih)
- Tombol Outline: `.btn-editorial-outline` (Background putih, border/teks `#C62828`, hover `#FEF2F2`)
- Sidebar Aktif: `.sidebar-active` (Background `#FEF2F2`, teks `#C62828`, border kiri 3px `#C62828`)
- Kartu: Border `#E5E5E5`, background `#FFFFFF`
- Badge Status: Gunakan `<x-badge>` dengan varian semantik (`indigo`, `emerald`, `rose`, `accent`, `slate`).

## 5. Disiplin Teknis
- Setiap pengerjaan UI tidak boleh menyentuh controller, routing, database, migrasi, atau model backend.
- Selalu uji dengan `npm run build` dan `php artisan view:clear` setelah perubahan CSS/Blade.
