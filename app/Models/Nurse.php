<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Nurse extends Model
{
     protected $fillable = [
        'user_id',
        'license_number',
        'specialty',
        'license_expiry_date',
        'phone',
        'available_hours',
    ];

    protected $casts = [
        'available_hours' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
