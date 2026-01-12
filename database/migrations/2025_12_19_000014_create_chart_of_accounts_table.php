<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('chart_of_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('account_type')->nullable(); // e.g., 'BlAccount' or 'PlAccount'
            $table->string('account_name');
            $table->string('account_code')->nullable();
            $table->string('group')->nullable();
            $table->string('group_category')->nullable();
            $table->string('cash_flow_category')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('chart_of_accounts');
    }
};
