<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppointmentRequest;
use App\Models\Appointment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\AppointmentResource;
use Illuminate\Routing\Controller;

class AppointmentController extends Controller
{

    public function __construct()
    {
        $this->middleware('can:view,appointment')->only('show');
        $this->middleware('can:update,appointment')->only('update', 'destroy');
    }

    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        $query = Appointment::with(['patient.user', 'doctor.user', 'nurse.user']);

        switch ($user->role) {
            case 'paciente':
                $patientId = $user->patient->id ?? null;
                if (!$patientId) {
                    return response()->json(['message' => 'Perfil de paciente não encontrado.'], 404);
                }
                $query->where('patient_id', $patientId);
                break;

            case 'medico':
                $doctorId = $user->doctor->id ?? null;
                if (!$doctorId) {
                    return response()->json(['message' => 'Perfil de médico não encontrado.'], 404);
                }
                $query->where('doctor_id', $doctorId);
                break;

            case 'enfermeiro':
                $nurseId = $user->nurse->id ?? null;
                if (!$nurseId) {
                    return response()->json(['message' => 'Perfil de enfermeiro não encontrado.'], 404);
                }
                $query->where('nurse_id', $nurseId);
                break;

            case 'admin':
                break;

            default:
                return response()->json(['message' => 'Função de usuário não reconhecida.'], 403);
        }

        $query->where('appointment_date', '>=', now());

        $appointments = $query->latest('appointment_date')->get();

        return AppointmentResource::collection($appointments);
    }

    public function store(StoreAppointmentRequest $request)
    {
        $patient = $request->user()->patient;

        if (!$patient) {
            return response()->json(['message' => 'Apenas pacientes podem criar agendamentos.'], 403);
        }

        $professionalId = $request->doctor_id ?? $request->nurse_id;
        $professionalKey = $request->doctor_id ? 'doctor_id' : 'nurse_id';

        $scheduledAt = $request->appointment_date;

        $hasConflict = Appointment::where($professionalKey, $professionalId)
            ->where('appointment_date', $scheduledAt)
            ->where('status', 'agendado')
            ->exists();

        if ($hasConflict) {
            return response()->json([
                'message' => 'O horário solicitado não está disponível para o profissional.',
                'error_code' => 'SCHEDULE_CONFLICT'
            ], 409);
        }


        try {
            $appointment = DB::transaction(function () use ($request, $patient, $professionalKey, $professionalId, $scheduledAt) {

                $data = $request->validated();

                return Appointment::create([
                    'patient_id' => $patient->id,
                    $professionalKey => $professionalId,
                    'appointment_date' => $scheduledAt,
                    'type' => $data['type'],
                    'notes' => $data['notes'],
                    'status' => 'agendado',
                    'link_meet' => null // Pode ser gerado posteriormente
                ]);
            });

            return response()->json([
                'message' => 'Agendamento criado com sucesso.',
                'appointment' => $appointment
            ], 201);

        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Falha ao processar o agendamento.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Appointment $appointment)
    {
        return new AppointmentResource($appointment->load(['patient.user', 'doctor.user', 'nurse.user']));
    }

    public function update(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'notes' => ['sometimes', 'string', 'max:500'],
            'status' => ['sometimes', 'string', 'in:cancelado'],
        ]);

        $appointment->update($validated);

        return response()->json([
            'message' => 'Agendamento atualizado com sucesso.',
            'appointment' => new AppointmentResource($appointment->load(['patient.user', 'doctor.user', 'nurse.user']))
        ]);
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->delete();

        return response()->json(['message' => 'Agendamento deletado com sucesso.'], 204); // 204 No Content
    }
}
