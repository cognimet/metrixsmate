<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    protected $guarded = [];

    protected $casts = [
        'issued_at' => 'datetime',
        'ocean_score' => 'decimal:2',
        'riasec_score' => 'decimal:2',
        'cognitive_score' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function generateCertificateNumber(): string
    {
        $prefix = 'MM';
        $year = date('Y');
        $random = strtoupper(bin2hex(random_bytes(4)));
        return "{$prefix}-{$year}-{$random}";
    }
}
