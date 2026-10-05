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
        Schema::table('loans', function (Blueprint $table) {
            $table->timestamp('pickup_deadline')->nullable()->after('due_date');
            $table->timestamp('approved_at')->nullable()->after('pickup_deadline');
            $table->timestamp('borrowed_at')->nullable()->after('approved_at');
            $table->text('rejection_reason')->nullable()->after('borrowed_at');
        });

        // Modify status column from enum to string to support full reservation lifecycle
        // (pending, approved, borrowed, returned, rejected, cancelled, expired, overdue)
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE loans MODIFY COLUMN status VARCHAR(30) NOT NULL DEFAULT 'pending'");
        } else {
            Schema::table('loans', function (Blueprint $table) {
                $table->string('status', 30)->default('pending')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE loans MODIFY COLUMN status ENUM('borrowed', 'returned', 'overdue') NOT NULL DEFAULT 'borrowed'");
        }

        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn(['pickup_deadline', 'approved_at', 'borrowed_at', 'rejection_reason']);
        });
    }
};
