<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@techperpus.com'],
            [
                'name' => 'Administrator Pustaka',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        User::firstOrCreate(
            ['email' => 'anggota@techperpus.com'],
            [
                'name' => 'Anggota Perpustakaan',
                'password' => Hash::make('password'),
                'role' => 'anggota',
            ]
        );
    }
}
