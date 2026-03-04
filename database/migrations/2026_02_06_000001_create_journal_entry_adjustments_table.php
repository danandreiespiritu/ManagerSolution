<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        // Drop and recreate if exists (for idempotency)
        Schema::dropIfExists('journal_entry_adjustments');
        
        Schema::create('journal_entry_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_entry_id')->constrained('journal_entries')->cascadeOnDelete();
            $table->foreignId('adjustment_entry_id')->constrained('journal_entries')->cascadeOnDelete();
            $table->unsignedBigInteger('business_id')->nullable()->index();
            $table->unsignedBigInteger('account_id')->nullable()->index();
            $table->decimal('debit_amount', 15, 2)->default(0);
            $table->decimal('credit_amount', 15, 2)->default(0);
            $table->enum('adjustment_type', ['debit_imbalance', 'credit_imbalance'])->comment('Type of adjustment made');
            $table->text('reason')->nullable()->comment('Reason for the adjustment');
            $table->boolean('is_applied')->default(false)->comment('Whether adjustment has been posted to ledger');
            $table->timestamps();

            // Ensure no duplicate adjustments for the same original entry and account
            $table->unique(['journal_entry_id', 'account_id', 'adjustment_type'], 'jea_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('journal_entry_adjustments');
    }
};
