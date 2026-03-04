<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('balance_sheet_reports', function (Blueprint $table) {
            if (! Schema::hasColumn('balance_sheet_reports', 'equation')) {
                $table->string('equation', 20)->default('standard')->after('accounting_method');
            }
        });
    }

    public function down(): void
    {
        Schema::table('balance_sheet_reports', function (Blueprint $table) {
            if (Schema::hasColumn('balance_sheet_reports', 'equation')) {
                $table->dropColumn('equation');
            }
        });
    }
};
