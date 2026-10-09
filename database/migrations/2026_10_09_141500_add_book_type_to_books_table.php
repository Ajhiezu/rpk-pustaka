<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            if (!Schema::hasColumn('books', 'book_type')) {
                $table->enum('book_type', ['nonfiksi', 'fiksi'])->default('nonfiksi')->after('collection_type');
                $table->index('book_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            if (Schema::hasColumn('books', 'book_type')) {
                $table->dropIndex(['book_type']);
                $table->dropColumn('book_type');
            }
        });
    }
};
