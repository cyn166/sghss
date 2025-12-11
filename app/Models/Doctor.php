<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Doctor extends Model
{
    protected $fillable = [
        'user_id',
        'crm',
        'specialty',
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
