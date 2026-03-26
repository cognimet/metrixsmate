<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizAccess extends Model
{
    protected $table = 'quiz_accesses';
    protected $guarded = [];

    protected $casts = [
        'granted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class)->nullable();
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class)->nullable();
    }

    /**
     * Scope to check if user has access to a quiz
     */
    public static function hasAccess(User $user, Quiz $quiz): bool
    {
        return self::where('user_id', $user->id)
            ->where('quiz_id', $quiz->id)
            ->exists();
    }

    /**
     * Grant access to a user for a quiz via payment
     */
    public static function grantViaPayment(User $user, Quiz $quiz, Payment $payment): self
    {
        return self::firstOrCreate(
            [
                'user_id' => $user->id,
                'quiz_id' => $quiz->id,
            ],
            [
                'payment_id' => $payment->id,
                'access_type' => 'payment',
                'granted_at' => now(),
            ]
        );
    }

    /**
     * Grant access to a user for a quiz via coupon
     */
    public static function grantViaCoupon(User $user, Quiz $quiz, Coupon $coupon): self
    {
        return self::firstOrCreate(
            [
                'user_id' => $user->id,
                'quiz_id' => $quiz->id,
            ],
            [
                'coupon_id' => $coupon->id,
                'access_type' => 'coupon',
                'granted_at' => now(),
            ]
        );
    }
}
