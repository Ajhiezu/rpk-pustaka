<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Default system operational settings
        Setting::firstOrCreate(
            ['key' => 'late_fine_per_day'],
            ['value' => '1000', 'description' => 'Denda keterlambatan pengembalian buku fisik per hari (Rupiah)']
        );
        Setting::firstOrCreate(
            ['key' => 'digital_loan_duration_days'],
            ['value' => '7', 'description' => 'Durasi standar peminjaman hak akses buku digital (hari)']
        );
        Setting::firstOrCreate(
            ['key' => 'physical_loan_duration_days'],
            ['value' => '14', 'description' => 'Batas maksimal durasi peminjaman eksemplar fisik buku (hari)']
        );

        $this->call([
            UserSeeder::class,
            // CategorySeeder::class,
            // LocationSeeder::class,
            // BookSeeder::class,
        ]);
    }
}
