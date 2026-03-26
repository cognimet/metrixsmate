<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_accesses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('quiz_id')->constrained('quizzes')->onDelete('cascade');
            $table->foreignId('payment_id')->nullable()->constrained('payments')->onDelete('set null');
            $table->foreignId('coupon_id')->nullable()->constrained('coupons')->onDelete('set null');
            $table->enum('access_type', ['payment', 'coupon', 'admin'])->default('payment');
            $table->dateTime('granted_at');
            $table->timestamps();

            $table->unique(['user_id', 'quiz_id']);
            $table->index(['quiz_id', 'access_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_accesses');
    }
};
