<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Appointment;
use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function patient_cannot_view_another_patients_appointment()
    {

        $userA = User::factory()->create(['role' => 'paciente']);
        Patient::factory()->create(['user_id' => $userA->id]);

        $userB = User::factory()->create(['role' => 'paciente']);
        $patientB = Patient::factory()->create(['user_id' => $userB->id]);

        $appointment = Appointment::factory()->create([
            'patient_id' => $patientB->id,
        ]);

        $token = $userA->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson('/api/appointments/' . $appointment->id);


        $response->assertStatus(403);

        $response->assertJson([
            'message' => 'This action is unauthorized.'
        ]);

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'patient_id' => $patientB->id,
        ]);
    }

}
