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
        Schema::table('recommendation_feedback_logs', function (Blueprint $table) {
            $table->foreign('school_recommendation_id')
                ->references('id')
                ->on('school_recommendations')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recommendation_feedback_logs', function (Blueprint $table) {
            $table->dropForeign(['school_recommendation_id']);
        });
    }
};
