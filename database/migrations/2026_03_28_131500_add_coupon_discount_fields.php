<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            // Add new coupon management fields
            $table->string('discount_type')->default('percentage')->after('code'); // percentage or fixed
            $table->decimal('discount_value', 10, 2)->default(0)->after('discount_type'); // discount amount
            $table->decimal('minimum_amount', 10, 2)->nullable()->after('discount_value'); // minimum purchase required
            $table->integer('uses_per_user')->nullable()->after('max_uses'); // how many times per user
            $table->text('description')->nullable()->after('uses_per_user'); // coupon description
            $table->renameColumn('times_used', 'usage_count'); // rename for consistency
        });
    }

    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropColumn(['discount_type', 'discount_value', 'minimum_amount', 'uses_per_user', 'description']);
            $table->renameColumn('usage_count', 'times_used');
        });
    }
};
