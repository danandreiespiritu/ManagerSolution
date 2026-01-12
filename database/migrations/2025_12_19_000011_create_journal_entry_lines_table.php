<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('journal_entry_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_entry_id')->constrained('journal_entries')->cascadeOnDelete();
            // `chart_of_accounts` may not exist yet at migration time; use unsignedBigInteger with index
            $table->unsignedBigInteger('account_id')->nullable()->index();
            $table->decimal('debit_amount', 15, 2)->default(0);
            $table->decimal('credit_amount', 15, 2)->default(0);
            $table->unsignedBigInteger('customer_id')->nullable()->index();
            $table->unsignedBigInteger('supplier_id')->nullable()->index();
            $table->timestamps();
        });

        // Add nullable foreign keys using separate statements to avoid ordering issues on some MySQL setups
        Schema::table('journal_entry_lines', function (Blueprint $table) {
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('journal_entry_lines', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropForeign(['supplier_id']);
        });
        Schema::dropIfExists('journal_entry_lines');
    }
};
