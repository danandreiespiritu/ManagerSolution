<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('balance_sheet_reports', function (Blueprint $table) {
            if (! Schema::hasColumn('balance_sheet_reports', 'from')) {
                $table->date('from')->nullable()->after('date');
            }

            if (! Schema::hasColumn('balance_sheet_reports', 'to')) {
                $table->date('to')->nullable()->after('from');
            }
        });
    }

    public function down(): void
    {
        Schema::table('balance_sheet_reports', function (Blueprint $table) {
            if (Schema::hasColumn('balance_sheet_reports', 'to')) {
                $table->dropColumn('to');
            }
            if (Schema::hasColumn('balance_sheet_reports', 'from')) {
                $table->dropColumn('from');
            }
        });
    }
};
