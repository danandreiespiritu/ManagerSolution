<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profit_and_loss_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('business_id')->constrained('businesses')->cascadeOnDelete();

            $table->string('title');
            $table->string('description')->nullable();
            $table->date('date_from');
            $table->date('date_to');

            $table->string('accounting_method', 20)->default('accrual');
            $table->string('rounding', 20)->default('off');
            $table->text('footer')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profit_and_loss_reports');
    }
};
