<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('journal_entry_lines')) {
            return;
        }

        Schema::table('journal_entry_lines', function (Blueprint $table) {
            if (! Schema::hasColumn('journal_entry_lines', 'account_code')) {
                $table->string('account_code', 100)->nullable()->after('account_id');
            }

            if (! Schema::hasColumn('journal_entry_lines', 'description')) {
                $table->text('description')->nullable()->after('cash_category');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('journal_entry_lines')) {
            return;
        }

        Schema::table('journal_entry_lines', function (Blueprint $table) {
            if (Schema::hasColumn('journal_entry_lines', 'account_code')) {
                $table->dropColumn('account_code');
            }

            if (Schema::hasColumn('journal_entry_lines', 'description')) {
                $table->dropColumn('description');
            }
        });
    }
};
