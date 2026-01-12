<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trial_balance_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('business_id')->constrained('businesses')->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->string('description')->nullable();
            $table->string('method', 20)->default('accrual');
            $table->date('from_date');
            $table->date('to_date');
            $table->json('comparative_columns')->nullable();
            $table->timestamps();
        });

        Schema::create('general_ledger_summary_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('business_id')->constrained('businesses')->cascadeOnDelete();
            $table->string('description')->nullable();
            $table->date('from_date');
            $table->date('to_date');
            $table->boolean('show_codes')->default(false);
            $table->boolean('exclude_zero')->default(false);
            $table->timestamps();
        });

        Schema::create('general_ledger_transaction_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('business_id')->constrained('businesses')->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->string('description')->nullable();
            $table->date('from_date');
            $table->date('to_date');
            $table->unsignedBigInteger('account_id')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('balance_sheet_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('business_id')->constrained('businesses')->cascadeOnDelete();
            $table->string('title');
            $table->date('date');
            $table->string('accounting_method', 20)->default('accrual');
            $table->string('layout')->default('assets-equals-liabilities-plus-equity');
            $table->string('description')->nullable();
            $table->json('columns')->nullable();
            $table->text('footer')->nullable();
            $table->timestamps();
        });

        Schema::create('profit_and_loss_actual_vs_budget_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('business_id')->constrained('businesses')->cascadeOnDelete();
            $table->string('title');
            $table->date('date_from');
            $table->date('date_to');
            $table->string('accounting_method', 20)->default('accrual');
            $table->json('lines');
            $table->text('footer')->nullable();
            $table->timestamps();
        });

        Schema::create('customer_summary_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('business_id')->constrained('businesses')->cascadeOnDelete();
            $table->string('title')->default('Customer Summary');
            $table->string('description')->nullable();
            $table->date('from_date');
            $table->date('to_date');
            $table->timestamps();
        });

        Schema::create('supplier_summary_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('business_id')->constrained('businesses')->cascadeOnDelete();
            $table->string('title')->default('Supplier Summary');
            $table->string('description')->nullable();
            $table->date('from_date');
            $table->date('to_date');
            $table->timestamps();
        });

        Schema::create('customer_unpaid_statement_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('business_id')->constrained('businesses')->cascadeOnDelete();
            $table->string('title');
            $table->date('statement_date');
            $table->timestamps();
        });

        Schema::create('supplier_unpaid_statement_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('business_id')->constrained('businesses')->cascadeOnDelete();
            $table->string('title');
            $table->date('statement_date');
            $table->timestamps();
        });

        Schema::create('customer_transaction_statement_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('business_id')->constrained('businesses')->cascadeOnDelete();
            $table->string('title');
            $table->date('from_date');
            $table->date('to_date');
            $table->timestamps();
        });

        Schema::create('supplier_transaction_statement_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('business_id')->constrained('businesses')->cascadeOnDelete();
            $table->string('title');
            $table->date('from_date');
            $table->date('to_date');
            $table->timestamps();
        });

        Schema::create('cash_flow_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('business_id')->constrained('businesses')->cascadeOnDelete();
            $table->string('description')->nullable();
            $table->string('method', 20)->default('indirect');
            $table->date('from');
            $table->date('to');
            $table->string('column_label')->nullable();
            $table->json('comparatives')->nullable();
            $table->timestamps();
        });

        Schema::create('changes_in_equity_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('business_id')->constrained('businesses')->cascadeOnDelete();
            $table->string('description')->nullable();
            $table->string('accounting_method', 20)->default('accrual');
            $table->date('from');
            $table->date('to');
            $table->string('column_label')->nullable();
            $table->json('comparatives')->nullable();
            $table->text('footer')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('changes_in_equity_reports');
        Schema::dropIfExists('cash_flow_reports');
        Schema::dropIfExists('supplier_transaction_statement_reports');
        Schema::dropIfExists('customer_transaction_statement_reports');
        Schema::dropIfExists('supplier_unpaid_statement_reports');
        Schema::dropIfExists('customer_unpaid_statement_reports');
        Schema::dropIfExists('supplier_summary_reports');
        Schema::dropIfExists('customer_summary_reports');
        Schema::dropIfExists('profit_and_loss_actual_vs_budget_reports');
        Schema::dropIfExists('balance_sheet_reports');
        Schema::dropIfExists('general_ledger_transaction_reports');
        Schema::dropIfExists('general_ledger_summary_reports');
        Schema::dropIfExists('trial_balance_reports');
    }
};
