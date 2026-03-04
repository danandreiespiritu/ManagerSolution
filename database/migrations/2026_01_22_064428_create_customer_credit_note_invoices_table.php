<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       if (!Schema::hasTable('customer_credit_note_invoices')) {
           Schema::create('customer_credit_note_invoices', function (Blueprint $table) {
               $table->id();
               $table->unsignedBigInteger('customer_credit_note_id');
               $table->unsignedBigInteger('invoice_id')->nullable();
               $table->unsignedBigInteger('business_id');
               $table->decimal('amount', 15, 2)->default(0);
               $table->timestamps();

               $table->foreign('customer_credit_note_id')
                     ->references('id')->on('customer_credit_notes')
                     ->onDelete('cascade');
           });
       }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_credit_note_invoices');
    }
};
