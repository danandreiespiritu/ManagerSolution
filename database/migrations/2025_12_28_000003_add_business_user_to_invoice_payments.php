<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('invoice_payments', function (Blueprint $table) {
            if (! Schema::hasColumn('invoice_payments', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete()->after('id');
            }
            if (! Schema::hasColumn('invoice_payments', 'business_id')) {
                $table->unsignedBigInteger('business_id')->nullable()->index()->after('user_id');
            }
        });
    }

    public function down()
    {
        Schema::table('invoice_payments', function (Blueprint $table) {
            if (Schema::hasColumn('invoice_payments', 'business_id')) {
                $table->dropColumn('business_id');
            }
            if (Schema::hasColumn('invoice_payments', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
        });
    }
};
