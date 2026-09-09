<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasterDescriptors extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_descriptors', function (Blueprint $table) {
            $table->id();
            $table->string('assessment_type', 255)->comment('ocean, riasec, cognitive');
            $table->string('result_type', 255)->comment('domains, all_facets, job_match_rate, cognitive_assessment, technical_assessment, growth_potential, organization_fit_forecast, flight_risk, soft_skills, ccs');
            $table->string('name', 255);  // Name of the descriptor
            $table->string('slug', 255); // Slug for the descriptor
            $table->string('code', 255); // Code for the descriptor
            $table->string('user_type', 25)->comment('employee, candidate');  // Employee or candidate
            $table->string('job_requirement_level', 25)->nullable();  // Level of job requirement
            $table->string('user_score_level', 25)->nullable();  // Level of user score
            $table->text('analysis')->nullable();  // Analysis details
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('master_descriptors');
    }
}
