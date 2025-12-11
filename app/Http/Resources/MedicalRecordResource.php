<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MedicalRecordResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'diagnosis' => $this->diagnosis,
            'treatment' => $this->treatment,
            'prescription' => $this->prescription,
            'notes' => $this->notes,

            'creator' => new UserResource($this->whenLoaded('creator')),
            'appointment_id' => $this->appointment_id,

            'appointment' => new AppointmentResource($this->whenLoaded('appointment')),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
