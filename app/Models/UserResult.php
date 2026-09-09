<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserResult extends Model
{
    protected $table = 'user_results';
    protected $guarded = [];

    /**
     * Get the user that owns this result
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
