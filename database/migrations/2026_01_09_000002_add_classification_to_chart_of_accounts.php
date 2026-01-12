<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        if (Schema::hasTable('chart_of_accounts') && !Schema::hasColumn('chart_of_accounts', 'classification')) {
            Schema::table('chart_of_accounts', function (Blueprint $table) {
                $table->string('classification')->nullable()->after('account_type');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('chart_of_accounts') && Schema::hasColumn('chart_of_accounts', 'classification')) {
            Schema::table('chart_of_accounts', function (Blueprint $table) {
                $table->dropColumn('classification');
            });
        }
    }
};
