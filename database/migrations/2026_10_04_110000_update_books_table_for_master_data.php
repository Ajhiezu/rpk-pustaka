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
        // 1. Add new columns as nullable first for safe migration of existing data
        Schema::table('books', function (Blueprint $table) {
            if (!Schema::hasColumn('books', 'book_code')) {
                $table->string('book_code', 50)->nullable()->after('id');
            }
            if (!Schema::hasColumn('books', 'language')) {
                $table->string('language', 50)->default('Indonesia')->after('isbn');
            }
            if (!Schema::hasColumn('books', 'page_count')) {
                $table->unsignedInteger('page_count')->nullable()->after('language');
            }
            if (!Schema::hasColumn('books', 'collection_type')) {
                $table->enum('collection_type', ['fisik', 'digital', 'fisik_digital'])->default('fisik')->after('description');
            }

            // Adjust existing columns to be nullable (avoid forcing fake/fabricated metadata)
            $table->unsignedBigInteger('location_id')->nullable()->change();
            $table->string('publisher')->nullable()->change();
            $table->integer('year')->nullable()->change();
            $table->string('isbn')->nullable()->change();
        });

        // 2. Backfill book_code for existing records so it can be strictly NOT NULL UNIQUE
        $existingBooks = DB::table('books')->whereNull('book_code')->orderBy('id')->get();
        foreach ($existingBooks as $index => $book) {
            $code = 'RPK-LEG-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT);
            DB::table('books')->where('id', $book->id)->update(['book_code' => $code]);
        }

        // 3. Set book_code to NOT NULL and UNIQUE
        Schema::table('books', function (Blueprint $table) {
            $table->string('book_code', 50)->nullable(false)->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropUnique(['book_code']);
            $table->dropColumn(['book_code', 'language', 'page_count', 'collection_type']);
            $table->unsignedBigInteger('location_id')->nullable(false)->change();
            $table->string('publisher')->nullable(false)->change();
            $table->integer('year')->nullable(false)->change();
            $table->string('isbn')->nullable(false)->change();
        });
    }
};
