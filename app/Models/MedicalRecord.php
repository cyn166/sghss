<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class MedicalRecord extends Model
{

    protected $table = 'medical_records';
    protected $fillable = [
        'appointment_id',
        'diagnosis',
        'treatment',
        'prescriptions',
        'notes',
        'who_created',
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
}
