<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Temporarily change column to VARCHAR to allow transitioning enum values
        DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(50) NOT NULL DEFAULT 'member'");

        // 2. Migrate existing 'staff' user records to 'admin'
        DB::table('users')->where('role', 'staff')->update(['role' => 'admin']);

        // 3. Migrate existing 'member' user records to 'anggota'
        DB::table('users')->where('role', 'member')->update(['role' => 'anggota']);

        // 4. Alter role column in users table strictly to ENUM('admin', 'anggota')
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'anggota') NOT NULL DEFAULT 'anggota'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(50) NOT NULL DEFAULT 'anggota'");
        DB::table('users')->where('role', 'anggota')->update(['role' => 'member']);
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'staff', 'member') NOT NULL DEFAULT 'member'");
    }
};
