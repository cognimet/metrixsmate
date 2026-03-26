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
        Schema::create('dynamic_schools', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('external_id')->nullable()->unique(); // API ID (Google Places, Maps, etc)
            $table->string('data_source'); // 'google_places', 'bing_maps', 'web_scrape', 'ai_discovered'
            $table->unsignedBigInteger('city_id');
            $table->unsignedBigInteger('state_id');
            $table->unsignedBigInteger('country_id');
            $table->string('type')->default('mainstream'); // mainstream, stem, arts, technical, business, social
            $table->string('board')->nullable(); // CBSE, ICSE, State Board, etc
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->decimal('rating', 3, 2)->default(0); // 0-5 rating
            $table->integer('rating_count')->default(0);
            $table->json('strengths')->nullable(); // Array of strengths
            $table->json('facilities')->nullable(); // Array of facilities
            $table->json('programs')->nullable(); // Array of programs offered
            $table->json('specializations')->nullable(); // Array of specializations
            $table->text('academic_performance')->nullable(); // Success rate, exam results
            $table->json('fees_range')->nullable(); // Min and max fees
            $table->text('additional_info')->nullable(); // Extra information from various sources
            $table->timestamp('last_verified_at')->nullable(); // When data was last verified
            $table->timestamp('last_refreshed_at')->nullable(); // When data was last fetched
            $table->boolean('is_verified')->default(false); // Manually verified
            $table->integer('relevance_score')->default(0); // For search ranking
            $table->timestamps();
            $table->softDeletes();

            // Indexes for common queries
            $table->index('city_id');
            $table->index('state_id');
            $table->index('country_id');
            $table->index('type');
            $table->index('data_source');
            $table->index('rating');
            $table->index('external_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dynamic_schools');
    }
};
