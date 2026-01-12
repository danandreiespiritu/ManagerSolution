<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplier_debit_note_bills', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('business_id')->nullable();
            $table->unsignedBigInteger('supplier_debit_note_id');
            $table->unsignedBigInteger('supplier_bill_id');
            $table->decimal('amount', 15, 2)->default(0);
            $table->timestamps();

            $table->index('supplier_debit_note_id');
            $table->index('supplier_bill_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_debit_note_bills');
    }
};
