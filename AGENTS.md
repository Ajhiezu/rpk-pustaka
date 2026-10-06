# RPK PUSTAKA IMM SAINTEKMU — DESIGN SYSTEM & UI MEMORY

Dokumen ini merupakan **sumber kebenaran tunggal (Single Source of Truth / Memory)** untuk seluruh identitas visual, UI/UX, dan implementasi frontend aplikasi **RPK PUSTAKA IMM SAINTEKMU (Digital Library)**.
Setiap kali ada permintaan perbaikan, revisi, atau penambahan fitur antarmuka, **baca dan ikuti aturan di bawah ini secara ketat**.

---

## 1. VISUAL IDENTITY & PHILOSOPHY

* **Karakter Desain:** Modern, Academic, Editorial, Minimalist, Professional, Clean.
* **Inspirasi Layout:** Harvard Library website (komposisi editorial, whitespace lapang, tipografi elegan, keterbacaan tinggi).
* **ATURAN MUTLAK WARNA:**
  * **JANGAN PERNAH** menggunakan warna cokelat (*brown*), krem (*beige*), atau terakota (*terracotta*).
  * **JANGAN PERNAH** menggunakan warna neon red, orange sebagai primary, atau gradient merah-kuning/gold.
  * Identitas visual adalah: **RED + GOLD + WHITE**.
* **Zero Backend Changes:** Setiap perubahan antarmuka **100% fokus pada Frontend (Blade, CSS, JS)**. Jangan mengubah route, controller, database, migration, model, API, atau business logic backend kecuali secara eksplisit diminta.

---

## 2. BRAND COLOR PALETTE & CODES

| Peran Warna | Hex Code | Kelas Tailwind / Token | Penggunaan & Rasio |
| :--- | :--- | :--- | :--- |
| **RPK RED (Primary)** | `#C62828` | `bg-primary`, `text-primary`, `border-primary` | Warna identitas brand utama. Digunakan untuk CTA primer (`.btn-editorial`), active navigation, search button, highlight link, outline border. |
| **RPK RED Hover** | `#A71D1D` | `bg-primary-dark`, `hover:bg-primary-hover` | Hover state tombol utama dan elemen interaktif primer. |
| **RPK RED Subtle Tint** | `#FEF2F2` | `bg-primary-light` | Background active sidebar (`.sidebar-active`), hover outline button, chip aktif. |
| **RPK GOLD (Secondary Accent)** | `#E5A11A` | `text-accent`, `bg-accent` | **Sangat selektif (1–5% visual weight)**. Terinspirasi dari bintang & lingkaran logo. Digunakan untuk: aksen bintang editorial, chip buku unggulan (*featured*), dan aksen status tertentu. **BUKAN** pengganti tombol primer. |
| **RPK GOLD Light Tint** | `#FFF9ED` | `bg-accent-light`, `border-[#FDE68A]` | Background badge buku rekomendasi / featured collection. |
| **WHITE (Main Canvas)** | `#FFFFFF` | `bg-white`, `bg-bg-main` | **Dominan (70–80% visual weight)**. Background utama halaman, kartu buku, panel form, navbar, modal. |
| **SOFT NEUTRAL** | `#F8F8F7` | `bg-neutral-surface` | Section alternating, metric stats band, table header, form background, subtle card surface. |
| **DARK TEXT (Primary)** | `#181818` | `text-neutral-dark` | Judul, heading editorial, teks navigasi utama, footer background institusi. |
| **BODY TEXT (Secondary)** | `#666666` | `text-neutral-body` | Paragraf, sinopsis buku, deskripsi label formulir. |
| **MUTED TEXT** | `#888888` | `text-neutral-muted` | Metadata sekunder, nomor ISBN, label tanggal, placeholder form. |
| **BORDER LINE** | `#E5E5E5` | `border-neutral-border` | Garis tepi kartu, pemisah tabel, border input form. |

### Semantic Status Colors (Bukan Brand Color):
* **Success:** `#2E7D32` (bg: `#EDF7ED`, border: `#C8E6C9`)
* **Danger / Error:** `#D32F2F` (bg: `#FDEDED`, border: `#FFCDD2`)
* **Warning:** `#E5A11A` (bg: `#FFF8E1`, border: `#FFE082`)
* **Info:** `#1976D2` (bg: `#E3F2FD`, border: `#BBDEFB`)

---

## 3. TYPOGRAPHY SYSTEM

* **Primary Font System:** `font-sans` → **Inter** (Google Fonts: 400, 500, 600, 700, 800)
  * Fallback: `Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif`.
  * **JANGAN GUNAKAN SERIF (Playfair Display)** sebagai font utama heading maupun body text.
* **Heading Hierarchy:**
  * H1: 32–48px desktop, 28–36px tablet, 26–32px mobile. Weight: 700 / 800 (`font-bold` / `font-extrabold`), tracking-tight.
  * H2: 28–36px. Weight: 700 (`font-bold`).
  * H3: 20–24px. Weight: 600–700 (`font-semibold` / `font-bold`).
  * H4: 16–20px. Weight: 600 (`font-semibold`).
* **Body / Controls:**
  * Paragraf, deskripsi, navbar, tombol, search placeholder, label, input, tabel, notifikasi: `font-sans`, line-height 1.5–1.7.
  * Weight standar: 400 (normal), 500 (medium), 600 (semibold), 700 (bold). **Hindari weight 300**.
  * Hindari tracking / letter-spacing berlebihan (hanya gunakan tracking-wide/wider halus untuk small uppercase tags/badges).

---

## 4. ASSET LOGO & FAVICON

* **Logo Asli:**
  * File: `public/images/logo-rpk.png` (dan `logo-rpk.jpg`).
  * Aturan penggunaan: Tampilkan logo original tanpa mengubah warna, tanpa filter shadow tebal, tanpa stretching (proporsional).
  * Komponen logo: `resources/views/components/application-logo.blade.php`.
* **Favicon Browser:**
  * File icon: `public/images/logo-rpk.ico` dan `public/favicon.ico`.
  * Selalu sertakan tag di dalam `<head>` layout:
    ```html
    <link rel="icon" type="image/x-icon" href="{{ asset('images/logo-rpk.ico') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/logo-rpk.ico') }}">
    ```

---

## 5. STANDARD KOMPONEN UI

### A. Tombol (Buttons)
* **Primary CTA:** `.btn-editorial` (atau `<x-primary-button>` / `<x-button variant="primary">`)
  * Background: `#C62828`, Text: `#FFFFFF`, Hover: `#A71D1D`.
* **Secondary / Outline CTA:** `.btn-editorial-outline` (atau `<x-secondary-button>`)
  * Background: `#FFFFFF`, Border: `#C62828`, Text: `#C62828`, Hover: `#FEF2F2`.
* **Accent Gold Button:** Hanya untuk aksi spesifik berstatus khusus (misal rating bintang/penghargaan), bukan pengganti tombol simpan/cari.

### B. Kartu Buku & Konten (Cards)
* Background: `#FFFFFF`.
* Border: `1px solid #E5E5E5`.
* Shadow: Ringan dan halus (`shadow-xs` / `shadow-sm`), jangan gunakan heavy black shadow.
* Hover: Transisi halus dengan elevasi mikro (`hover:border-primary/40 hover:shadow-md`).

### C. Sidebar (Panel Admin & Petugas)
* Background: `#FFFFFF` atau `#F8F8F7`.
* Nav Link Normal: Text `#666666`, hover background `#F8F8F7`, hover text `#181818`.
* Nav Link Aktif (`.sidebar-active`):
  * Background: `#FEF2F2`.
  * Text: `#C62828` font semi-bold.
  * Left border indicator: `3px solid #C62828`.

### D. Tabel Data (`<x-table>`)
* Header table (`<thead>`): Background `#F8F8F7`, border `#E5E5E5`, teks uppercase font-bold 11px `#181818` / `#666666`.
* Baris data (`<tr>`): Hover background `#F8F8F7`, border pembatas `#E5E5E5`.
* State kosong: Blok `@empty` dengan teks italic `#888888`.

### E. Formulir Input (`<x-input>`, `<x-text-input>`)
* Normal state: Background `#FFFFFF`, Border `1px solid #E5E5E5`, Text `#181818`, Placeholder `#888888`.
* Focus state: Ring halus RPK Red (`focus:ring-2 focus:ring-primary/20 focus:border-primary`).
* Disabled state: Background `#F8F8F7`, cursor not-allowed.

---

## 6. DAFTAR FILE-FILE PENTING FRONTEND

* **Design Tokens & CSS:**
  * `resources/css/app.css` (Definisi `@theme`, utility classes, Google Fonts import).
* **Master Layouts:**
  * `resources/views/layouts/app.blade.php` (Layout utama aplikasi, admin, member, sidebar, header).
  * `resources/views/layouts/auth.blade.php` (Layout split editorial untuk login dan register).
  * `resources/views/layouts/guest.blade.php` (Layout auth fallback / reset password).
  * `resources/views/welcome.blade.php` (Halaman landing page publik & pencarian katalog).
* **Komponen Inti (`resources/views/components/`):**
  * `application-logo.blade.php` (Logo RPK Pustaka resmi).
  * `primary-button.blade.php` & `button.blade.php` (Tombol primer RPK Red).
  * `secondary-button.blade.php` (Tombol outline RPK Red).
  * `badge.blade.php` (Badge status dan varian brand).
  * `alert.blade.php` (Pesan peringatan / feedback status).
  * `card.blade.php` (Container kartu editorial).
  * `table.blade.php` (Tabel data akademik).
  * `input.blade.php` & `text-input.blade.php` (Form controls).

---

## 7. CHECKLIST SEBELUM MENYELESAIKAN REVISI UI

Setiap kali melakukan pekerjaan frontend:
- [ ] Tidak ada kode warna hex cokelat (`#5C2114`, `#8C7A70`, `#5A4E47`, dll).
- [ ] Tidak ada kode warna hex krem/beige (`#FAF7F5`, `#E3D9D2`, `#F2ECE7`, `#B1552C`, dll).
- [ ] Primary brand adalah RPK Red (`#C62828`).
- [ ] Secondary accent adalah RPK Gold (`#E5A11A`) dan proporsinya kecil (1–5%).
- [ ] Background utama adalah Putih (`#FFFFFF`) dan Soft Neutral (`#F8F8F7`).
- [ ] Logo RPK Pustaka dan Favicon tampil dengan benar.
- [ ] Tipografi modern sans-serif Inter tetap konsisten di seluruh website (bebas font-serif dominan).
- [ ] Menjalankan `npm run build` dan `php artisan view:clear` untuk memastikan build produksi tanpa error.
- [ ] Zero changes pada backend controller, database, migrations, dan routes.
