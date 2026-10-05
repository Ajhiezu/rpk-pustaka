<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Article;
use App\Models\Essay;

$user = User::first();
if ($user) {
    Article::firstOrCreate(
        ['slug' => 'penerapan-kecerdasan-buatan-dalam-preservasi-naskah'],
        [
            'user_id' => $user->id,
            'title' => 'Penerapan Kecerdasan Buatan dalam Preservasi Naskah Perpustakaan',
            'excerpt' => 'Kajian mendalam tentang pemanfaatan algoritma OCR dan visi komputer untuk restorasi dan pengindeksan dokumen naskah kuno.',
            'content' => 'Perkembangan teknologi kecerdasan buatan telah membuka peluang baru dalam preservasi dokumen naskah bersejarah.',
            'status' => 'published',
            'published_at' => now(),
        ]
    );

    Article::firstOrCreate(
        ['slug' => 'metodologi-riset-ilmiah-di-era-digital'],
        [
            'user_id' => $user->id,
            'title' => 'Metodologi Riset Ilmiah & Pengelolaan Sitasi di Era Digital',
            'excerpt' => 'Panduan komprehensif penulisan karya ilmiah bereputasi, teknik manajemen referensi, dan integritas akademik.',
            'content' => 'Dalam era penulisan ilmiah berbasis data, pentingnya ketepatan pengutipan dan metodologi yang transparan menjadi prioritas utama.',
            'status' => 'published',
            'published_at' => now(),
        ]
    );

    Essay::firstOrCreate(
        ['title' => 'Rekonstruksi Semangat Literasi Sains dan Nilai-Nilai Kemanusiaan'],
        [
            'user_id' => $user->id,
            'submission_type' => 'online',
            'content' => 'Literasi sains bukan sekadar membaca formula dan statistik, melainkan mengasah daya kritis dan refleksi etik terhadap perkembangan peradaban.',
            'status' => 'published',
        ]
    );

    Essay::firstOrCreate(
        ['title' => 'Dialektika Peradaban: Membaca Ulang Warisan Intelektual Islam'],
        [
            'user_id' => $user->id,
            'submission_type' => 'online',
            'content' => 'Melacak benang merah pemikiran filosofis dan kontribusi keilmuan era keemasan Islam dalam membentuk lanskap sains modern.',
            'status' => 'published',
        ]
    );

    echo "Sample data generated successfully!\n";
}
