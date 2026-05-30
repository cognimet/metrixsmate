<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    /**
     * Validate a coupon and return discount details.
     * Does NOT grant access — the discount is applied during payment initiation.
     */
    public function apply(Request $request)
    {
        $request->validate([
            'code'   => 'required|string',
            'amount' => 'nullable|numeric|min:0',
        ]);

        $coupon = Coupon::where('code', strtoupper($request->code))->first();

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid coupon code.',
            ], 404);
        }

        if (!$coupon->isValid()) {
            if ($coupon->expires_at && $coupon->expires_at->isPast()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This coupon has expired.',
                ], 400);
            }

            return response()->json([
                'success' => false,
                'message' => 'This coupon is no longer valid.',
            ], 400);
        }

        $originalPrice = (float) ($request->amount ?? 15);

        if ($coupon->discount_type === 'percentage') {
            $discountAmount  = round($originalPrice * $coupon->discount_value / 100, 2);
            $discountLabel   = $coupon->discount_value . '% off';
        } else {
            $discountAmount  = min((float) $coupon->discount_value, $originalPrice);
            $discountLabel   = '$' . number_format($coupon->discount_value, 2) . ' off';
        }

        $discountedPrice = round($originalPrice - $discountAmount, 2);

        return response()->json([
            'success'          => true,
            'coupon_code'      => $coupon->code,
            'discount_type'    => $coupon->discount_type,
            'discount_value'   => $coupon->discount_value,
            'original_price'   => $originalPrice,
            'discount_amount'  => $discountAmount,
            'discounted_price' => $discountedPrice,
            'discount_label'   => $discountLabel,
            'message'          => "Coupon applied! {$discountLabel}. New total: $" . number_format($discountedPrice, 2),
        ]);
    }

    /**
     * Validate coupon without applying (lightweight check)
     */
    public function validate(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $coupon = Coupon::where('code', strtoupper($request->code))->first();

        if (!$coupon || !$coupon->isValid()) {
            return response()->json([
                'valid'   => false,
                'message' => 'Invalid or expired coupon code.',
            ]);
        }

        return response()->json([
            'valid'          => true,
            'discount_type'  => $coupon->discount_type,
            'discount_value' => $coupon->discount_value,
            'message'        => 'Valid coupon code.',
        ]);
    }
}
