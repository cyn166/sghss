<?php

namespace App\Policies;

use App\Models\User;
use App\Models\MedicalRecord;

class MedicalRecordPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->role === 'admin') {
            return true;
        }
        return null;
    }

    public function view(User $user, MedicalRecord $medicalRecord): bool
    {
        return $medicalRecord->creator->id === $user->id;
    }

    public function update(User $user, MedicalRecord $medicalRecord): bool
    {
        return $medicalRecord->creator->id === $user->id;
    }

    public function delete(User $user, MedicalRecord $medicalRecord): bool
    {
        return $this->update($user, $medicalRecord);
    }
}
