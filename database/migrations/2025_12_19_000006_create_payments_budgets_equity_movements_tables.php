<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('payment_date');
            $table->decimal('amount', 15, 2);
            $table->enum('payment_type', ['Customer','Supplier']);
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->unsignedBigInteger('account_id')->nullable()->index();
            $table->string('reference')->nullable();
            $table->timestamps();
        });

        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // `chart_of_accounts` may not be available yet; use unsignedBigInteger and index.
            $table->unsignedBigInteger('account_id')->nullable()->index();
            $table->foreignId('accounting_period_id')->constrained('accounting_periods')->cascadeOnDelete();
            $table->decimal('budget_amount', 15, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('equity_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('movement_date');
            // `chart_of_accounts` may not be available yet; use unsignedBigInteger and index.
            $table->unsignedBigInteger('account_id')->nullable()->index();
            $table->decimal('amount', 15, 2);
            $table->enum('movement_type', ['Contribution','Withdrawal']);
            $table->foreignId('accounting_period_id')->constrained('accounting_periods')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('equity_movements');
        Schema::dropIfExists('budgets');
        Schema::dropIfExists('payments');
    }
};
