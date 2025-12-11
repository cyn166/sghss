<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{

    protected $table = 'appointments';
    protected $fillable = [
        'patient_id',
        'doctor_id',
        'nurse_id',
        'type',
        'link_meet',
        'status',
        'notes',
        'appointment_date',
    ];

    protected $casts = [
        'appointment_date' => 'datetime',
    ];


    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function nurse(): BelongsTo
    {
        return $this->belongsTo(Nurse::class);
    }
}
