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
        Schema::create('recommendation_feedback_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->unsignedBigInteger('school_recommendation_id');
            $table->string('feedback_type'); // viewed, clicked, liked, disliked, interested, enrolled
            $table->text('feedback_context')->nullable(); // Additional context about the feedback
            $table->json('user_assessment_snapshot')->nullable(); // Snapshot of user's assessment at feedback time
            $table->json('school_data_snapshot')->nullable(); // Snapshot of school data at feedback time
            $table->string('user_agent')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();

            // Indexes for analytics and learning
            $table->index('user_id');
            $table->index('feedback_type');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recommendation_feedback_logs');
    }
};
