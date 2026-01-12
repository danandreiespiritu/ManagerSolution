<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        $tables = [
            'chart_of_accounts',
            'customers',
            'customer_invoices',
            'customer_credit_notes',
            'suppliers',
            'supplier_bills',
            'supplier_debit_notes',
            'journal_entries',
            'journal_entry_lines',
            'payments',
            'budgets',
            'equity_movements',
            'report_definitions',
            'report_filters',
            'accounting_periods',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $t) use ($table) {
                    if (!Schema::hasColumn($table, 'business_id')) {
                        $t->foreignId('business_id')->nullable()->constrained('businesses')->onDelete('cascade');
                    }
                });
            }
        }
    }

    public function down()
    {
        $tables = [
            'chart_of_accounts',
            'customers',
            'customer_invoices',
            'customer_credit_notes',
            'suppliers',
            'supplier_bills',
            'supplier_debit_notes',
            'journal_entries',
            'journal_entry_lines',
            'payments',
            'budgets',
            'equity_movements',
            'report_definitions',
            'report_filters',
            'accounting_periods',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'business_id')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->dropConstrainedForeignId('business_id');
                });
            }
        }
    }
};
