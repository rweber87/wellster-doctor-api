<?php

namespace Database\Factories;

use App\Models\Assignment;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssignmentFactory extends Factory
{
    protected $model = Assignment::class;

    public function definition(): array
    {
        return [
            'doctor_id' => Doctor::inRandomOrder()->first()->id ?? Doctor::factory(),
            'patient_id' => Patient::inRandomOrder()->first()->id ?? Patient::factory(),
            'appointment_date' => $this->faker->dateTimeBetween('-1 year', '+1 year'),
        ];
    }
}
