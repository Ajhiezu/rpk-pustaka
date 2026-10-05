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
            $table->decimal('price', 12, 2)->default(0)->after('available_stock');
            $table->enum('fine_type', ['fixed', 'multiplier', 'custom'])->default('fixed')->after('price');
            $table->string('fine_value')->default('50000')->after('fine_type')->comment('Nominal fixed or multiplier (e.g. 1x, 2x)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn(['price', 'fine_type', 'fine_value']);
        });
    }

};
