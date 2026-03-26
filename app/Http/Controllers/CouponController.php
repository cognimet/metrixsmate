<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\QuizAccess;
use App\Models\Quiz;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    /**
     * Validate and apply a coupon code (grants access to ALL assessments)
     */
    public function apply(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $user = auth()->user();

        // Check if user already has access to all assessments
        $quizzes = Quiz::all();
        $allAccessible = $quizzes->every(fn($quiz) => QuizAccess::hasAccess($user, $quiz));
        
        if ($allAccessible) {
            return response()->json([
                'success' => false,
                'message' => 'You already have access to all assessments.',
            ], 400);
        }

        // Find the coupon
        $coupon = Coupon::where('code', strtoupper($request->code))->first();

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid coupon code.',
            ], 404);
        }

        // Validate coupon
        if (!$coupon->isValid()) {
            if ($coupon->used_at) {
                return response()->json([
                    'success' => false,
                    'message' => 'This coupon has already been used.',
                ], 400);
            }

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

        // Use the coupon
        if (!$coupon->use($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to apply coupon. Please try again.',
            ], 500);
        }

        // Grant access to ALL quizzes
        foreach ($quizzes as $quiz) {
            QuizAccess::grantViaCoupon($user, $quiz, $coupon);
        }

        return response()->json([
            'success' => true,
            'message' => 'Coupon applied successfully! You now have access to all assessments.',
            'redirect' => route('dashboard'),
        ]);
    }

    /**
     * Validate coupon without applying
     */
    public function validate(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $coupon = Coupon::where('code', strtoupper($request->code))->first();

        if (!$coupon || !$coupon->isValid()) {
            return response()->json([
                'valid' => false,
                'message' => 'Invalid or expired coupon code.',
            ]);
        }

        return response()->json([
            'valid' => true,
            'message' => 'Valid coupon code.',
        ]);
    }
}
