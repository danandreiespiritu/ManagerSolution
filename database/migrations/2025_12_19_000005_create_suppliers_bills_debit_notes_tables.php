<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('supplier_code')->nullable()->index();
            $table->string('supplier_name');
            $table->string('email')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('supplier_bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('bill_number')->index();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->date('bill_date');
            $table->date('due_date')->nullable();
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->string('status')->default('Unpaid');
            $table->timestamps();
        });

        Schema::create('supplier_debit_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('debit_note_number')->index();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->date('debit_date');
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->text('reason')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('supplier_debit_notes');
        Schema::dropIfExists('supplier_bills');
        Schema::dropIfExists('suppliers');
    }
};
