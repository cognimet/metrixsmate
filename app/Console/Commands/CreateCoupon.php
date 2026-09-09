<?php

namespace App\Console\Commands;

use App\Models\Coupon;
use Illuminate\Console\Command;

class CreateCoupon extends Command
{
    protected $signature = 'coupon:create {--code=} {--count=1}';
    protected $description = 'Create a coupon code that grants access to all assessments';

    public function handle()
    {
        $code = $this->option('code');
        $count = (int) $this->option('count');

        $createdCoupons = [];
        for ($i = 0; $i < $count; $i++) {
            $couponCode = $code ?? Coupon::generateCode();
            
            $coupon = Coupon::create([
                'code' => $couponCode,
                'is_active' => true,
                'max_uses' => 1,
                'expires_at' => now()->addDays(30), // Expires in 30 days
            ]);

            $createdCoupons[] = $coupon;
            
            // If user provided specific code, don't loop again
            if ($code) {
                break;
            }
        }

        $this->info("✓ Successfully created " . count($createdCoupons) . " coupon(s) for all assessments");
        $this->newLine();

        foreach ($createdCoupons as $coupon) {
            $this->line("Code: <fg=yellow>{$coupon->code}</> | Expires: {$coupon->expires_at->format('Y-m-d H:i:s')}");
        }

        return 0;
    }
}
