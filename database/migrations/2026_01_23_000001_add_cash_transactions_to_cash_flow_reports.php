<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('cash_flow_reports', function (Blueprint $table) {
            $table->json('cash_transactions')->nullable()->after('comparatives');
        });
    }

    public function down()
    {
        Schema::table('cash_flow_reports', function (Blueprint $table) {
            $table->dropColumn('cash_transactions');
        });
    }
};
