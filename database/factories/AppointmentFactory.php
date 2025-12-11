<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Appointment>
 */
class AppointmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
{
    return [
        'appointment_date' => $this->faker->dateTimeBetween('tomorrow', '+1 month'),
        'type' => $this->faker->randomElement(['presencial', 'telemedicina']),
        'notes' => $this->faker->sentence(),
        'status' => 'scheduled',
        'doctor_id' => null,
        'nurse_id' => null,
    ];
}
}
