<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Performance Indexes Migration
 * Adds composite and single-column indexes for high-frequency query columns.
 * Compatible with Laravel 10/11 (no Doctrine dependency).
 */
return new class extends Migration
{
    public function up(): void
    {
        // --- LOANS TABLE ---
        Schema::table('loans', function (Blueprint $table) {
            if (!$this->indexExists('loans', 'loans_user_id_status_index')) {
                $table->index(['user_id', 'status'], 'loans_user_id_status_index');
            }
            if (!$this->indexExists('loans', 'loans_status_due_date_index')) {
                $table->index(['status', 'due_date'], 'loans_status_due_date_index');
            }
            if (!$this->indexExists('loans', 'loans_status_loan_type_index')) {
                $table->index(['status', 'loan_type'], 'loans_status_loan_type_index');
            }
            if (!$this->indexExists('loans', 'loans_created_at_index')) {
                $table->index('created_at', 'loans_created_at_index');
            }
            if (!$this->indexExists('loans', 'loans_pickup_deadline_index')) {
                $table->index('pickup_deadline', 'loans_pickup_deadline_index');
            }
        });

        // --- LOAN_DETAILS TABLE ---
        Schema::table('loan_details', function (Blueprint $table) {
            if (!$this->indexExists('loan_details', 'loan_details_loan_id_status_index')) {
                $table->index(['loan_id', 'status'], 'loan_details_loan_id_status_index');
            }
            if (!$this->indexExists('loan_details', 'loan_details_book_id_status_index')) {
                $table->index(['book_id', 'status'], 'loan_details_book_id_status_index');
            }
        });

        // --- FINES TABLE ---
        Schema::table('fines', function (Blueprint $table) {
            if (!$this->indexExists('fines', 'fines_loan_id_status_index')) {
                $table->index(['loan_id', 'status'], 'fines_loan_id_status_index');
            }
        });

        // --- BOOKS TABLE ---
        Schema::table('books', function (Blueprint $table) {
            if (!$this->indexExists('books', 'books_available_stock_index')) {
                $table->index('available_stock', 'books_available_stock_index');
            }
            if (!$this->indexExists('books', 'books_category_collection_type_index')) {
                $table->index(['category_id', 'collection_type'], 'books_category_collection_type_index');
            }
        });

        // --- SETTINGS TABLE ---
        Schema::table('settings', function (Blueprint $table) {
            if (!$this->indexExists('settings', 'settings_key_index')) {
                $table->index('key', 'settings_key_index');
            }
        });
    }

    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            if ($this->indexExists('loans', 'loans_user_id_status_index'))    $table->dropIndex('loans_user_id_status_index');
            if ($this->indexExists('loans', 'loans_status_due_date_index'))   $table->dropIndex('loans_status_due_date_index');
            if ($this->indexExists('loans', 'loans_status_loan_type_index'))  $table->dropIndex('loans_status_loan_type_index');
            if ($this->indexExists('loans', 'loans_created_at_index'))        $table->dropIndex('loans_created_at_index');
            if ($this->indexExists('loans', 'loans_pickup_deadline_index'))   $table->dropIndex('loans_pickup_deadline_index');
        });

        Schema::table('loan_details', function (Blueprint $table) {
            if ($this->indexExists('loan_details', 'loan_details_loan_id_status_index')) $table->dropIndex('loan_details_loan_id_status_index');
            if ($this->indexExists('loan_details', 'loan_details_book_id_status_index')) $table->dropIndex('loan_details_book_id_status_index');
        });

        Schema::table('fines', function (Blueprint $table) {
            if ($this->indexExists('fines', 'fines_loan_id_status_index')) $table->dropIndex('fines_loan_id_status_index');
        });

        Schema::table('books', function (Blueprint $table) {
            if ($this->indexExists('books', 'books_available_stock_index'))              $table->dropIndex('books_available_stock_index');
            if ($this->indexExists('books', 'books_category_collection_type_index'))     $table->dropIndex('books_category_collection_type_index');
        });

        Schema::table('settings', function (Blueprint $table) {
            if ($this->indexExists('settings', 'settings_key_index')) $table->dropIndex('settings_key_index');
        });
    }

    /**
     * Check if an index already exists using raw SHOW INDEX query.
     * Compatible with Laravel 10+ (no Doctrine dependency).
     */
    private function indexExists(string $table, string $indexName): bool
    {
        $indexes = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$indexName]);
        return !empty($indexes);
    }
};
