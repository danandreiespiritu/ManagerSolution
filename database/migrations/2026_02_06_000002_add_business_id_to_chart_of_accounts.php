<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('chart_of_accounts', function (Blueprint $table) {
            if (!Schema::hasColumn('chart_of_accounts', 'business_id')) {
                $table->foreignId('business_id')->nullable()->constrained()->cascadeOnDelete()->after('user_id');
            }
            if (!Schema::hasColumn('chart_of_accounts', 'account_group')) {
                $table->string('account_group')->nullable()->after('account_code');
            }
        });
    }

    public function down()
    {
        Schema::table('chart_of_accounts', function (Blueprint $table) {
            if (Schema::hasColumn('chart_of_accounts', 'business_id')) {
                $table->dropForeignKeyIfExists(['business_id']);
                $table->dropColumn('business_id');
            }
            if (Schema::hasColumn('chart_of_accounts', 'account_group')) {
                $table->dropColumn('account_group');
            }
        });
    }
};
