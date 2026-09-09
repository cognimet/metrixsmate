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
        Schema::create('quiz_domain_value_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_domain_value_id')->constrained('quiz_domain_values')->onDelete('cascade');
            $table->string('title');
            $table->integer('order')->nullable();
            $table->integer('min_points')->nullable();
            $table->integer('max_points')->nullable();
        
            $table->tinyInteger('is_question_have_image')->default(0);
            $table->text('question_image_url')->nullable();
            $table->tinyInteger('is_option_have_image')->default(0);
        
            $table->json('options')->nullable();
            $table->integer('correct_answer')->nullable();
            $table->integer('set_number')->nullable();
            $table->integer('level_of_difficulty')->nullable();
        
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_domain_value_questions');
    }
};
