<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Auth\Access\Response;


class AppointmentPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->role === 'admin') {
            return true;
        }
        return null;
    }

    public function view(User $user, Appointment $appointment): bool
    {
        if ($user->role === 'paciente' && optional($user->patient)->id === $appointment->patient_id) {
            return true;
        }

        $isAssigned =
            (optional($user->doctor)->id === $appointment->doctor_id) ||
            (optional($user->nurse)->id === $appointment->nurse_id);

        return $isAssigned;
    }

    public function update(User $user, Appointment $appointment): bool
    {
        $isPatientOwner = optional($user->patient)->id === $appointment->patient_id;

        return $isPatientOwner && $appointment->status !== 'concluido';
    }

    public function delete(User $user, Appointment $appointment): bool
    {
        return $this->update($user, $appointment);
    }
}
