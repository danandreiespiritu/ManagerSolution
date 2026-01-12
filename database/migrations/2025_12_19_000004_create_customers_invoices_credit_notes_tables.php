<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('customer_code')->nullable()->index();
            $table->string('customer_name');
            $table->string('email')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('customer_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('invoice_number')->index();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->date('invoice_date');
            $table->date('due_date')->nullable();
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->string('status')->default('Unpaid'); // Unpaid | Paid | PartiallyPaid
            $table->timestamps();
        });

        Schema::create('customer_credit_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('credit_note_number')->index();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->date('credit_date');
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->text('reason')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('customer_credit_notes');
        Schema::dropIfExists('customer_invoices');
        Schema::dropIfExists('customers');
    }
};
