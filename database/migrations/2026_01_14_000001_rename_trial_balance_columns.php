<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Rename columns to match model/controller expectations
        if (Schema::hasTable('trial_balance_reports')) {
            Schema::table('trial_balance_reports', function (Blueprint $table) {
                if (Schema::hasColumn('trial_balance_reports', 'from_date')) {
                    $table->renameColumn('from_date', 'date_from');
                }

                if (Schema::hasColumn('trial_balance_reports', 'to_date')) {
                    $table->renameColumn('to_date', 'date_to');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('trial_balance_reports')) {
            Schema::table('trial_balance_reports', function (Blueprint $table) {
                if (Schema::hasColumn('trial_balance_reports', 'date_from')) {
                    $table->renameColumn('date_from', 'from_date');
                }

                if (Schema::hasColumn('trial_balance_reports', 'date_to')) {
                    $table->renameColumn('date_to', 'to_date');
                }
            });
        }
    }
};
