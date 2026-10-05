<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Location;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locations = [
            ['name' => 'Rak A-1', 'description' => 'Area Buku Komunikasi & Psikologi'],
            ['name' => 'Rak B-1', 'description' => 'Area Buku Pendidikan & Filsafat'],
            ['name' => 'Rak C-1', 'description' => 'Area Buku Manajemen & Bisnis'],
            ['name' => 'Rak D-1', 'description' => 'Area Buku Teknologi & Ekonomi'],
            ['name' => 'Rak E-1', 'description' => 'Area Buku Sastra & Sejarah'],
        ];

        foreach ($locations as $loc) {
            Location::firstOrCreate(
                ['name' => $loc['name']],
                ['description' => $loc['description']]
            );
        }
    }
}
