<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('quizzes')->onDelete('cascade');
            $table->string('code')->unique();
            $table->integer('max_uses')->default(1); // 1 = single use, null = unlimited
            $table->integer('times_used')->default(0);
            $table->boolean('is_active')->default(true);
            $table->dateTime('expires_at')->nullable();
            $table->foreignId('used_by')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('used_at')->nullable();
            $table->json('metadata')->nullable(); // Store additional info like discount percentage, etc
            $table->timestamps();

            $table->index(['code', 'quiz_id']);
            $table->index(['quiz_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
