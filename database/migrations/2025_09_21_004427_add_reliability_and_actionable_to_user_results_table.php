<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_results', function (Blueprint $table) {
            // Reliability score (numeric, e.g., 0–100 or 0–1 scale)
            $table->decimal('reliability_score', 5, 2)->nullable()->after('percentage');

            // Actionable insights (long text to store recommendations)
            $table->text('actionable_insights')->nullable()->after('level_description');
        });
    }

    public function down(): void
    {
        Schema::table('user_results', function (Blueprint $table) {
            $table->dropColumn(['reliability_score', 'actionable_insights']);
        });
    }
};
