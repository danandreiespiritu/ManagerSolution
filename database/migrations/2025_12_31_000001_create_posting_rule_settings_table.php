<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('posting_rule_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->nullable()->constrained('businesses')->cascadeOnDelete();
            $table->string('rule_name');
            $table->boolean('enabled')->default(true);
            $table->json('config')->nullable();
            $table->timestamps();
            $table->unique(['business_id','rule_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posting_rule_settings');
    }
};
