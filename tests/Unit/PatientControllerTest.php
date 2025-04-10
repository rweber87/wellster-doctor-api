<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PatientControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_return_unassigned_patients()
    {
        $unassignedPatients = Patient::factory(3)->create();
        $assignedPatient = Patient::factory()->create();
        Assignment::factory()->create(['patient_id' => $assignedPatient->id]);

        $response = $this->getJson('/api/unassigned-patients');

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }
}
