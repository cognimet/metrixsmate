<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('free_ai_searches')->default(3)->comment('Free AI-Powered searches remaining');
            $table->integer('free_assessment_searches')->default(3)->comment('Free Assessment-Based searches remaining');
            $table->integer('paid_search_tokens')->default(0)->comment('Paid tokens for additional searches (1 token = 1 search)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['free_ai_searches', 'free_assessment_searches', 'paid_search_tokens']);
        });
    }
};
