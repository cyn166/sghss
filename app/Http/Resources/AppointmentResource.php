<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UserResource;

class AppointmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'appointment_date' => $this->appointment_date,
            'type' => $this->type,
            'status' => $this->status,
            'notes' => $this->notes,

            'patient' => new UserResource($this->whenLoaded('patient')->user),
            'doctor' => $this->whenLoaded('doctor', function () {
                return $this->doctor ? new UserResource($this->doctor->user) : null;
            }),
            'nurse' => $this->whenLoaded('nurse', function () {
                return $this->nurse ? new UserResource($this->nurse->user) : null;
            }),

            'patient_id' => $this->patient_id,
            'doctor_id' => $this->doctor_id,
            'nurse_id' => $this->nurse_id,

            'created_at' => $this->created_at,
        ];
    }
}
