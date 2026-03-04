<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('journal_entry_lines') && ! Schema::hasColumn('journal_entry_lines', 'cash_category')) {
            Schema::table('journal_entry_lines', function (Blueprint $table) {
                $table->string('cash_category', 100)->nullable()->after('account_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('journal_entry_lines') && Schema::hasColumn('journal_entry_lines', 'cash_category')) {
            Schema::table('journal_entry_lines', function (Blueprint $table) {
                $table->dropColumn('cash_category');
            });
        }
    }
};
