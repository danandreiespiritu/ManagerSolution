<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('chart_of_accounts', function (Blueprint $table) {
            $table->boolean('is_control_account')->default(false)->after('is_active');
        });
    }

    public function down()
    {
        Schema::table('chart_of_accounts', function (Blueprint $table) {
            $table->dropColumn('is_control_account');
        });
    }
};
