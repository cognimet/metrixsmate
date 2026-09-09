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
        Schema::create('school_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->unsignedBigInteger('dynamic_school_id');
            $table->unsignedBigInteger('city_id');
            $table->integer('rank')->default(0); // 1-based ranking
            $table->decimal('compatibility_score', 5, 2)->default(0); // 0-100 score
            $table->decimal('riasec_match_score', 5, 2)->default(0);
            $table->decimal('cognitive_match_score', 5, 2)->default(0);
            $table->decimal('ocean_match_score', 5, 2)->default(0);
            $table->decimal('academic_performance_score', 5, 2)->default(0);
            $table->decimal('location_proximity_score', 5, 2)->default(0);
            $table->text('ai_reasoning'); // AI-generated explanation
            $table->json('match_factors')->nullable(); // Array of match reasons
            $table->json('strengths_alignment')->nullable(); // Which strengths match
            $table->json('personality_alignment')->nullable(); // Which personality traits align
            $table->integer('user_feedback')->nullable(); // -1 (dislike), 0 (neutral), 1 (like), 2 (enrolled)
            $table->text('user_feedback_comment')->nullable();
            $table->timestamp('user_feedback_at')->nullable();
            $table->integer('view_count')->default(0); // How many times user viewed this recommendation
            $table->timestamp('viewed_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Indexes for common queries
            $table->index('user_id');
            $table->index('city_id');
            $table->index('compatibility_score');
            $table->index('rank');
            $table->unique(['user_id', 'dynamic_school_id']); // One recommendation per school per user
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_recommendations');
    }
};
