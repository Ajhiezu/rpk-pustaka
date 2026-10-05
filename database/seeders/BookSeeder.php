<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Book;
use App\Models\Category;
use App\Models\Location;
use Illuminate\Support\Str;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedClientBooks();
        $this->seedDummyBooks();
    }

    /**
     * DATA CLIENT
     * Buku yang bersumber langsung dari data/dokumen yang diberikan oleh Client.
     */
    protected function seedClientBooks(): void
    {
        $categoryPsikologi = Category::where('slug', 'psikologi')->first();
        $categoryBisnis = Category::where('slug', 'bisnis')->first();

        $clientBooks = [
            [
                'book_code' => 'RPK-CLI-001',
                'title' => 'Psikologi Gelap',
                'slug' => 'psikologi-gelap',
                'author' => 'James W. Williams',
                'category_id' => $categoryPsikologi?->id ?? 1,
                'location_id' => null,
                'publisher' => null,
                'year' => 2019,
                'isbn' => null,
                'language' => 'Indonesia',
                'page_count' => null,
                'collection_type' => 'digital',
                'stock' => 0,
                'available_stock' => 0,
                'price' => 0,
                'fine_type' => 'fixed',
                'fine_value' => '0',
                'pdf_path' => 'books/pdf/test_sample_book.pdf',
                'description' => 'Buku referensi psikologi yang mengulas taktik manipulasi emosional, persuasi terselubung, dan mekanisme pertahanan psikologis dalam interaksi sosial.',
            ],
            [
                'book_code' => 'RPK-CLI-002',
                'title' => '6 Prinsip dan 7 Strategi Persuasi',
                'slug' => '6-prinsip-dan-7-strategi-persuasi',
                'author' => 'Ahmad Mubarok',
                'category_id' => $categoryBisnis?->id ?? 1,
                'location_id' => null,
                'publisher' => null,
                'year' => null,
                'isbn' => null,
                'language' => 'Indonesia',
                'page_count' => null,
                'collection_type' => 'digital',
                'stock' => 0,
                'available_stock' => 0,
                'price' => 0,
                'fine_type' => 'fixed',
                'fine_value' => '0',
                'pdf_path' => 'books/pdf/test_sample_book.pdf',
                'description' => 'Kajian strategis mengenai prinsip-prinsip komunikasi persuasif dalam dunia bisnis, negosiasi kemitraan, dan kepemimpinan manajerial.',
            ],
        ];

        foreach ($clientBooks as $data) {
            Book::updateOrCreate(
                ['book_code' => $data['book_code']],
                $data
            );
        }
    }

    /**
     * DATA DUMMY / TESTING
     * Koleksi fiktif berbahasa Indonesia untuk pengujian antarmuka dan fungsionalitas sistem.
     * Tidak menggunakan nama penulis nyata dan tidak mengarang nomor ISBN.
     */
    protected function seedDummyBooks(): void
    {
        $categories = Category::all()->keyBy('slug');
        $locations = Location::all();
        $locDefault = $locations->first()?->id;

        $dummyBooks = [
            [
                'book_code' => 'RPK-DUM-001',
                'title' => 'Dasar-Dasar Komunikasi Efektif',
                'author' => 'Andi Pratama',
                'cat_slug' => 'komunikasi',
                'collection_type' => 'fisik_digital',
                'stock' => 4,
                'has_pdf' => true,
                'description' => 'Buku panduan praktis mengenai prinsip dasar komunikasi interpersonal, mendengarkan aktif, dan penyampaian pesan yang jelas dalam interaksi akademik maupun profesional.',
            ],
            [
                'book_code' => 'RPK-DUM-002',
                'title' => 'Pengantar Psikologi Sosial',
                'author' => 'Rina Maharani',
                'cat_slug' => 'psikologi',
                'collection_type' => 'fisik',
                'stock' => 5,
                'has_pdf' => false,
                'description' => 'Pembahasan komprehensif mengenai dinamika perilaku individu dalam konteks sosial, hubungan antarkelompok, dan pengaruh norma sosial terhadap tindakan manusia.',
            ],
            [
                'book_code' => 'RPK-DUM-003',
                'title' => 'Strategi Belajar Efektif bagi Mahasiswa',
                'author' => 'Budi Setiawan',
                'cat_slug' => 'pendidikan',
                'collection_type' => 'digital',
                'stock' => 0,
                'has_pdf' => true,
                'description' => 'Kumpulan metode dan teknik manajemen kognitif untuk meningkatkan efektivitas pemahaman materi perkuliahan secara mandiri, terarah, dan berkelanjutan.',
            ],
            [
                'book_code' => 'RPK-DUM-004',
                'title' => 'Dasar-Dasar Manajemen Organisasi',
                'author' => 'Fajar Nugraha',
                'cat_slug' => 'manajemen',
                'collection_type' => 'fisik',
                'stock' => 3,
                'has_pdf' => false,
                'description' => 'Pengantar tata kelola struktur organisasi, pembagian kewenangan kerja, dan teknik koordinasi tim dalam pencapaian target kinerja lembaga.',
            ],
            [
                'book_code' => 'RPK-DUM-005',
                'title' => 'Seni Berbicara di Depan Umum',
                'author' => 'Siti Amalia',
                'cat_slug' => 'komunikasi',
                'collection_type' => 'fisik_digital',
                'stock' => 6,
                'has_pdf' => true,
                'description' => 'Panduan sistematis menyusun naskah presentasi, mengelola kecemasan panggung, dan membangun kepercayaan diri saat berorasi di hadapan publik.',
            ],
            [
                'book_code' => 'RPK-DUM-006',
                'title' => 'Pengantar Filsafat untuk Pemula',
                'author' => 'Dimas Ramadhan',
                'cat_slug' => 'filsafat',
                'collection_type' => 'fisik',
                'stock' => 2,
                'has_pdf' => false,
                'description' => 'Pengenalan dasar mengenai metode berpikir kritis, peta aliran pemikiran filosofis utama dunia, dan relevansi refleksi filsafat dalam kehidupan modern.',
            ],
            [
                'book_code' => 'RPK-DUM-007',
                'title' => 'Metode Penelitian untuk Mahasiswa',
                'author' => 'Nanda Permata',
                'cat_slug' => 'pendidikan',
                'collection_type' => 'fisik_digital',
                'stock' => 5,
                'has_pdf' => true,
                'description' => 'Langkah-langkah terstruktur dalam merancang proposal penelitian, teknik pengumpulan data kualitatif dan kuantitatif, serta penulisan laporan akhir.',
            ],
            [
                'book_code' => 'RPK-DUM-008',
                'title' => 'Literasi Digital di Era Informasi',
                'author' => 'Rizky Maulana',
                'cat_slug' => 'teknologi',
                'collection_type' => 'digital',
                'stock' => 0,
                'has_pdf' => true,
                'description' => 'Ulasan kritis mengenai kemampuan evaluasi informasi digital, penangkalan disinformasi daring, dan etika keamanan data pribadi di ruang siber.',
            ],
            [
                'book_code' => 'RPK-DUM-009',
                'title' => 'Kepemimpinan dan Pengembangan Diri',
                'author' => 'Putri Anggraini',
                'cat_slug' => 'pengembangan-diri',
                'collection_type' => 'fisik',
                'stock' => 4,
                'has_pdf' => false,
                'description' => 'Kajian mendalam mengenai pemetaan potensi kepribadian, kecerdasan emosional, serta teknik memotivasi tim kerja dalam lingkungan dinamis.',
            ],
            [
                'book_code' => 'RPK-DUM-010',
                'title' => 'Dasar-Dasar Ekonomi Kreatif',
                'author' => 'Arif Hidayat',
                'cat_slug' => 'ekonomi',
                'collection_type' => 'fisik_digital',
                'stock' => 3,
                'has_pdf' => true,
                'description' => 'Pengantar sektor ekonomi berbasis inovasi gagasan, perlindungan hak kekayaan intelektual, dan pengembangan ekosistem usaha kreatif lokal.',
            ],
            [
                'book_code' => 'RPK-DUM-011',
                'title' => 'Menulis Karya Ilmiah dengan Baik',
                'author' => 'Hendra Saputra',
                'cat_slug' => 'pendidikan',
                'collection_type' => 'digital',
                'stock' => 0,
                'has_pdf' => true,
                'description' => 'Petunjuk teknis penulisan artikel ilmiah, kaidah sitasi akademik, penyusunan kepustakaan, dan pencegahan plagiarisme naskah.',
            ],
            [
                'book_code' => 'RPK-DUM-012',
                'title' => 'Pengantar Ilmu Komunikasi',
                'author' => 'Dewi Lestari Ramli',
                'cat_slug' => 'komunikasi',
                'collection_type' => 'fisik',
                'stock' => 4,
                'has_pdf' => false,
                'description' => 'Teori dan paradigma fundamental proses transmisi informasi, interaksi simbolik, dan pengaruh media komunikasi massa di masyarakat.',
            ],
            [
                'book_code' => 'RPK-DUM-013',
                'title' => 'Manajemen Waktu untuk Mahasiswa',
                'author' => 'Ilham Pratama',
                'cat_slug' => 'pengembangan-diri',
                'collection_type' => 'fisik_digital',
                'stock' => 5,
                'has_pdf' => true,
                'description' => 'Strategi penentuan skala prioritas studi, teknik memutus kebiasaan menunda pekerjaan, dan penciptaan ritme keseimbangan produktivitas harian.',
            ],
            [
                'book_code' => 'RPK-DUM-014',
                'title' => 'Dasar-Dasar Kewirausahaan',
                'author' => 'Maya Kartika',
                'cat_slug' => 'bisnis',
                'collection_type' => 'fisik',
                'stock' => 3,
                'has_pdf' => false,
                'description' => 'Panduan identifikasi peluang usaha baru, perumusan model bisnis teruji, serta strategi peluncuran produk inovatif ke pasar sasaran.',
            ],
            [
                'book_code' => 'RPK-DUM-015',
                'title' => 'Sejarah Perkembangan Pemikiran Modern',
                'author' => 'Aditya Wibowo',
                'cat_slug' => 'sejarah',
                'collection_type' => 'fisik',
                'stock' => 2,
                'has_pdf' => false,
                'description' => 'Napak tilas peristiwa penting dan pergeseran paradigma intelektual yang mendasari pembentukan tatanan peradaban dunia kontemporer.',
            ],
        ];

        foreach ($dummyBooks as $item) {
            $cat = $categories->get($item['cat_slug']) ?? $categories->first();
            $isDigital = $item['collection_type'] === 'digital';

            Book::updateOrCreate(
                ['book_code' => $item['book_code']],
                [
                    'title' => $item['title'],
                    'slug' => Str::slug($item['title']),
                    'author' => $item['author'],
                    'category_id' => $cat?->id ?? 1,
                    'location_id' => $isDigital ? null : $locDefault,
                    'publisher' => null, // Metadata tidak dikarang
                    'year' => null,      // Metadata tidak dikarang
                    'isbn' => null,      // Metadata tidak dikarang
                    'language' => 'Indonesia',
                    'page_count' => null,// Metadata tidak dikarang
                    'collection_type' => $item['collection_type'],
                    'stock' => $item['stock'],
                    'available_stock' => $item['stock'],
                    'price' => 50000,
                    'fine_type' => 'fixed',
                    'fine_value' => '50000',
                    'pdf_path' => $item['has_pdf'] ? 'books/pdf/test_sample_book.pdf' : null,
                    'description' => $item['description'],
                ]
            );
        }
    }
}
