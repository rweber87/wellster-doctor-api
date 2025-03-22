<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Doctor;
use App\Models\Indication;
use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DoctorControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_show_a_doctor()
    {
        $doctor = Doctor::factory()->create();

        $response = $this->getJson("/api/doctors/{$doctor->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $doctor->id,
                'first_name' => $doctor->first_name,
                'last_name' => $doctor->last_name,
            ]);
    }

    /** @test */
    public function it_can_return_patients_for_a_doctor_sorted_by_last_name()
    {
        $doctor = Doctor::factory()->create();
        $patients = Patient::factory(3)->create()->sortBy('last_name');

        foreach ($patients as $patient) {
            Assignment::create([
                'doctor_id' => $doctor->id,
                'patient_id' => $patient->id,
                'appointment_date' => now()->addDays(rand(1, 30)),
            ]);
        }

        $response = $this->getJson("/api/doctors/{$doctor->id}/patients?sort_by=last_name");

        $response->assertStatus(200)
            ->assertJsonCount(3);

        $patientsArray = $response->json();
        $lastNameOrder = array_column($patientsArray, 'last_name');

        $this->assertEquals($lastNameOrder, collect($lastNameOrder)->sort()->values()->toArray());
    }

    /** @test */
    public function it_can_return_patients_for_a_doctor_sorted_by_appointment_date()
    {
        $doctor = Doctor::factory()->create();
        $patients = Patient::factory(3)->create();

        foreach ($patients as $patient) {
            Assignment::create([
                'doctor_id' => $doctor->id,
                'patient_id' => $patient->id,
                'appointment_date' => now()->addDays(rand(1, 30)),
            ]);
        }

        $response = $this->getJson("/api/doctors/{$doctor->id}/patients?sort_by=appointment_date");

        $response->assertStatus(200)
            ->assertJsonCount(3);

        $patientsArray = $response->json();
        $appointmentDates = array_column($patientsArray, 'appointment_date');

        $this->assertEquals($appointmentDates, collect($appointmentDates)->sort()->values()->toArray());
    }

    /** @test */
    public function it_can_return_unassigned_patients_matching_doctors_indications()
    {
        $doctor = Doctor::factory()->create();
        $indication = Indication::factory()->create(['name' => 'Diabetes']);
        $doctor->indications()->attach($indication->id);

        $matchingPatient = Patient::factory()->create();
        $matchingPatient->indications()->attach($indication->id);

        $nonMatchingPatient = Patient::factory()->create();
        $response = $this->getJson("/api/doctors/{$doctor->id}/matches");

        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonFragment(['id' => $matchingPatient->id]);
    }

    /** @test */
    public function it_can_assign_a_patient_to_a_doctor()
    {
        $doctor = Doctor::factory()->create();
        $patient = Patient::factory()->create();
        $indication = Indication::factory()->create();

        $doctor->indications()->attach($indication->id);
        $patient->indications()->attach($indication->id);

        $response = $this->postJson("/api/doctors/{$doctor->id}/assign-patient/{$patient->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'Patient assigned to doctor successfully.']);
    }

    /** @test */
    public function it_prevents_assigning_a_patient_to_multiple_doctors()
    {
        $doctor1 = Doctor::factory()->create();
        $doctor2 = Doctor::factory()->create();
        $patient = Patient::factory()->create();
        $indication = Indication::factory()->create();

        $doctor1->indications()->attach($indication->id);
        $doctor2->indications()->attach($indication->id);
        $patient->indications()->attach($indication->id);

        Assignment::create([
            'doctor_id' => $doctor1->id,
            'patient_id' => $patient->id,
            'appointment_date' => now()->addDays(5),
        ]);

        $response = $this->postJson("/api/doctors/{$doctor2->id}/assign-patient/{$patient->id}");

        $response->assertStatus(400)
            ->assertJsonFragment(['error' => 'This patient is already assigned to a doctor.']);
    }
}
