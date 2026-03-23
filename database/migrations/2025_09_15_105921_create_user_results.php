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
        Schema::create('user_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('descriptor_id');
            $table->string('assessment_type', 255)->comment('ocean, riasec, cognitive, technical');
            $table->string('result_type', 255)->comment('behaviour_fit_rate, soft_skill_match_rate, job_match_rate, cognitive_assessment, growth_potential, organizational_fit_forecast, flight_risk, soft_skills, ccs');
            $table->string('name', 255)->nullable();
            $table->string('slug', 255)->nullable(); 
            $table->string('code', 255)->nullable();
            $table->text('description')->nullable();
            $table->double('score', 8, 2)->default(0);
            $table->double('percentage', 8, 2)->default(0);
            $table->integer('level')->default(0);
            $table->string('level_description', 255)->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('descriptor_id')->references('id')->on('master_descriptors')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_results');
    }
};
