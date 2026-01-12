<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        if (!Schema::hasTable('journal_entries')) {
            Schema::create('journal_entries', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->date('entry_date');
                $table->string('narration')->nullable();
                $table->string('reference_type')->nullable(); // Invoice | CreditNote | DebitNote | Payment | Adjustment
                $table->unsignedBigInteger('reference_id')->nullable();
                $table->text('description')->nullable();
                $table->foreignId('accounting_period_id')->nullable()->constrained('accounting_periods')->nullOnDelete();
                $table->timestamps();
                $table->unsignedBigInteger('created_by')->nullable()->index();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('journal_entry_lines');
        Schema::dropIfExists('journal_entries');
    }
};
