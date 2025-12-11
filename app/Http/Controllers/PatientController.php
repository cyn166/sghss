<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class PatientController extends Controller
{


    public function show($id)
    {

        $user = Auth::user();

        if ($user->role === 'paciente') {
            return response()->json(['message' => 'Acesso negado.'], 403);
        }

        $patient = Patient::with('user')->find($id);

        if (!$patient) {
            return response()->json(['message' => 'Paciente não encontrado.'], 404);
        }

        $appointmentsCount = $patient->appointments()->count();
        $appointments = $patient->appointments()->with(['doctor.user', 'nurse.user'])->get();

        return response()->json([
            'id' => $patient->id,
            'user' => [
                'id' => $patient->user->id,
                'name' => $patient->user->name,
                'email' => $patient->user->email,
                'role' => $patient->user->role,
            ],
            'patient_details' => [
                'address' => $patient->address,
                'phone' => $patient->phone,
                'date_of_birth' => $patient->date_of_birth,
                'blood_type' => $patient->blood_type,
                'emergency_contact' => $patient->emergency_contact,
            ],
            'total_appointments' => $appointmentsCount,
            'appointments' => $appointments->map(function ($appointment) {
                return [
                    'id' => $appointment->id,
                    'appointment_date' => $appointment->appointment_date,
                    'doctor' => $appointment->doctor ? [
                        'id' => $appointment->doctor->id,
                        'name' => $appointment->doctor->user->name,
                        'email' => $appointment->doctor->user->email,
                    ] : null,
                    'nurse' => $appointment->nurse ? [
                        'id' => $appointment->nurse->id,
                        'name' => $appointment->nurse->user->name,
                        'email' => $appointment->nurse->user->email,
                    ] : null,
                    'status' => $appointment->status,
                ];
            }),
        ], 200);
    }
}
