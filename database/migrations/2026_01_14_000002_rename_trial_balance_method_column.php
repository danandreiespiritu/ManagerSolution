<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('trial_balance_reports')) {
            Schema::table('trial_balance_reports', function (Blueprint $table) {
                if (Schema::hasColumn('trial_balance_reports', 'method')) {
                    $table->renameColumn('method', 'accounting_method');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('trial_balance_reports')) {
            Schema::table('trial_balance_reports', function (Blueprint $table) {
                if (Schema::hasColumn('trial_balance_reports', 'accounting_method')) {
                    $table->renameColumn('accounting_method', 'method');
                }
            });
        }
    }
};
