<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add Hindi title/description columns to quizzes table
        Schema::table('quizzes', function (Blueprint $table) {
            $table->string('title_hi')->nullable()->after('title');
            $table->text('description_hi')->nullable()->after('description');
        });

        // Add Hindi title column to quiz_domain_values table
        Schema::table('quiz_domain_values', function (Blueprint $table) {
            $table->string('title_hi')->nullable()->after('title');
        });

        // Add Hindi title and options columns to quiz_domain_value_questions table
        Schema::table('quiz_domain_value_questions', function (Blueprint $table) {
            $table->string('title_hi')->nullable()->after('title');
            $table->json('options_hi')->nullable()->after('options');
        });
    }

    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropColumn(['title_hi', 'description_hi']);
        });

        Schema::table('quiz_domain_values', function (Blueprint $table) {
            $table->dropColumn('title_hi');
        });

        Schema::table('quiz_domain_value_questions', function (Blueprint $table) {
            $table->dropColumn(['title_hi', 'options_hi']);
        });
    }
};
