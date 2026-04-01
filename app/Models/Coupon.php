<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Coupon extends Model
{
    protected $guarded = [];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
        'metadata' => 'array',
        'discount_value' => 'decimal:2',
        'minimum_amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'used_by')->nullable();
    }

    /**
     * Check if coupon is valid (not expired, not exhausted, active)
     */
    public function isValid(): bool
    {
        // Check if all uses exhausted (null max_uses = unlimited)
        if ($this->max_uses !== null && $this->times_used >= $this->max_uses) {
            return false;
        }

        // Check if expired
        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        // Check if currently active
        return $this->is_active;
    }

    /**
     * Use the coupon — increments times_used; marks as exhausted when max_uses reached
     */
    public function use(User $user): bool
    {
        if (!$this->isValid()) {
            return false;
        }

        $newCount = $this->times_used + 1;
        $updates  = ['times_used' => $newCount];

        // Only mark as fully used when limited supply is exhausted
        if ($this->max_uses !== null && $newCount >= $this->max_uses) {
            $updates['used_by'] = $user->id;
            $updates['used_at'] = now();
        }

        $this->update($updates);

        return true;
    }

    /**
     * Scope for active coupons
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                // Allow unlimited coupons (null max_uses) or ones not yet exhausted
                $q->whereNull('max_uses')
                    ->orWhereRaw('times_used < max_uses');
            })
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    /**
     * Generate a random coupon code
     */
    public static function generateCode(): string
    {
        return strtoupper(substr(md5(uniqid(rand(), true)), 0, 12));
    }
}
