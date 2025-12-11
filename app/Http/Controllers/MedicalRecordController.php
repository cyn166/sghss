<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMedicalRecordRequest;
use App\Models\Appointment;
use App\Models\MedicalRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class MedicalRecordController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view,medicalRecord')->only('show');
        $this->middleware('can:update,medicalRecord')->only('update', 'destroy');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMedicalRecordRequest $request)
    {
        $user = Auth::user();

        if (!in_array($user->role, ['medico', 'enfermeiro'])) {
            return response()->json([
                'message' => 'Apenas médicos ou enfermeiros podem criar prontuários.'
            ], Response::HTTP_FORBIDDEN);
        }

        $appointment = Appointment::with(['doctor', 'nurse'])
            ->findOrFail($request->appointment_id);

        $isDoctorAssigned = $user->role === 'medico' && optional($appointment->doctor)->user_id === $user->id;
        $isNurseAssigned = $user->role === 'enfermeiro' && optional($appointment->nurse)->user_id === $user->id;

        if (!$isDoctorAssigned && !$isNurseAssigned) {
            return response()->json([
                'message' => 'Você não está autorizado a atender este agendamento.'
            ], Response::HTTP_FORBIDDEN);
        }

        if ($appointment->status === 'concluido') {
            return response()->json([
                'message' => 'Este agendamento já foi concluído e tem um prontuário associado.'
            ], Response::HTTP_CONFLICT);
        }

        try {
            $record = DB::transaction(function () use ($request, $user, $appointment) {

                $record = MedicalRecord::create([
                    'appointment_id' => $appointment->id,
                    'diagnosis' => $request->diagnosis,
                    'treatment' => $request->treatment,
                    'prescriptions' => $request->prescription,
                    'notes' => $request->notes,
                    'who_created' => $user->role,
                ]);

                $appointment->status = 'concluido';
                $appointment->save();

                return $record;
            });

            return response()->json([
                'message' => 'Atendimento e prontuário salvos com sucesso. Agendamento concluído.',
                'medical_record' => $record->load('appointment')
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Falha interna ao salvar o prontuário.',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(MedicalRecord $medicalRecord)
    {
        $medicalRecord->load(['creator', 'appointment.patient.user']);

        return new MedicalRecordResource($medicalRecord);
    }

    public function update(Request $request, MedicalRecord $medicalRecord)
    {
        $validated = $request->validate([
            'diagnosis' => ['sometimes', 'string'],
            'treatment' => ['sometimes', 'string'],
            'prescription' => ['sometimes', 'nullable', 'string'],
            'notes' => ['sometimes', 'nullable', 'string', 'max:500'],
        ]);

        $medicalRecord->update($validated);

        return response()->json([
            'message' => 'Prontuário atualizado com sucesso.',
            'medical_record' => new MedicalRecordResource($medicalRecord->load(['creator', 'appointment.patient.user']))
        ]);
    }

    public function destroy(MedicalRecord $medicalRecord)
    {
        $medicalRecord->delete();

        return response()->json(['message' => 'Prontuário deletado com sucesso.'], Response::HTTP_NO_CONTENT);
    }
}
