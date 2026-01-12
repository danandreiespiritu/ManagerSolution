<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        if (Schema::hasTable('journal_entry_lines') && ! Schema::hasColumn('journal_entry_lines', 'description')) {
            Schema::table('journal_entry_lines', function (Blueprint $table) {
                $table->text('description')->nullable()->after('account_id');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('journal_entry_lines') && Schema::hasColumn('journal_entry_lines', 'description')) {
            Schema::table('journal_entry_lines', function (Blueprint $table) {
                $table->dropColumn('description');
            });
        }
    }
};
