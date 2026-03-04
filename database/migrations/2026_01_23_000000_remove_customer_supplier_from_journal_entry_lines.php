<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        if (Schema::hasColumn('journal_entry_lines', 'customer_id')) {
            Schema::table('journal_entry_lines', function (Blueprint $table) {
                $table->dropForeign(['customer_id']);
                $table->dropColumn('customer_id');
            });
        }

        if (Schema::hasColumn('journal_entry_lines', 'supplier_id')) {
            Schema::table('journal_entry_lines', function (Blueprint $table) {
                $table->dropForeign(['supplier_id']);
                $table->dropColumn('supplier_id');
            });
        }
    }

    public function down()
    {
        $hasCustomer = Schema::hasColumn('journal_entry_lines', 'customer_id');
        $hasSupplier = Schema::hasColumn('journal_entry_lines', 'supplier_id');

        Schema::table('journal_entry_lines', function (Blueprint $table) use ($hasCustomer, $hasSupplier) {
            if (! $hasCustomer) {
                $table->unsignedBigInteger('customer_id')->nullable()->index();
            }
            if (! $hasSupplier) {
                $table->unsignedBigInteger('supplier_id')->nullable()->index();
            }
        });

        Schema::table('journal_entry_lines', function (Blueprint $table) use ($hasCustomer, $hasSupplier) {
            if (! $hasCustomer) {
                $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
            }
            if (! $hasSupplier) {
                $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('set null');
            }
        });
    }
};
