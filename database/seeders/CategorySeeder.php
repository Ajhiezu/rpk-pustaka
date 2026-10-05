<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Pendidikan',
            'Psikologi',
            'Komunikasi',
            'Filsafat',
            'Manajemen',
            'Bisnis',
            'Teknologi',
            'Pengembangan Diri',
            'Ekonomi',
            'Sastra',
            'Sejarah',
        ];

        foreach ($categories as $name) {
            Category::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            );
        }
    }
}
