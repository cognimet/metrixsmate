<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('certificate_number')->unique();
            $table->string('full_name');
            $table->decimal('ocean_score', 5, 2)->default(0);
            $table->decimal('riasec_score', 5, 2)->default(0);
            $table->decimal('cognitive_score', 5, 2)->default(0);
            $table->string('holland_code', 10)->nullable();
            $table->string('top_personality_trait')->nullable();
            $table->string('top_cognitive_strength')->nullable();
            $table->string('file_path')->nullable();
            $table->timestamp('issued_at');
            $table->timestamps();

            $table->index('certificate_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
