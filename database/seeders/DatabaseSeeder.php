<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\Doctor;
use App\Models\Indication;
use App\Models\Patient;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        $indications = [
            'Diabetes', 'Hair Loss', 'Hypertension', 'Asthma', 'Migraines',
            'Acne', 'Arthritis', 'Depression', 'Allergies', 'Obesity',
        ];

        foreach ($indications as $indicationName) {
            Indication::create(['name' => $indicationName]);
        }

        $indicationIds = Indication::pluck('id')->toArray();

        $doctors = Doctor::factory(10)->create()->each(function ($doctor) use ($faker, $indicationIds) {
            $doctorIndications = $faker->randomElements($indicationIds, rand(2, 4));
            $doctor->indications()->attach($doctorIndications);
        });

        $patients = Patient::factory(30)->create()->each(function ($patient) use ($faker, $indicationIds) {
            $patientIndications = $faker->randomElements($indicationIds, rand(1, 2));
            $patient->indications()->attach($patientIndications);
        });

        $assignedPatients = [];

        foreach ($doctors as $doctor) {
            $treatableIndications = $doctor->indications->pluck('id')->toArray();

            $eligiblePatients = $patients->filter(function ($patient) use ($treatableIndications, $assignedPatients) {
                return count(array_intersect($patient->indications->pluck('id')->toArray(), $treatableIndications)) > 0
                    && ! in_array($patient->id, $assignedPatients);
            });

            $patientsToAssignCount = min(rand(2, 5), $eligiblePatients->count());

            $patientsToAssign = $eligiblePatients->random($patientsToAssignCount);

            foreach ($patientsToAssign as $patient) {
                Assignment::create([
                    'doctor_id' => $doctor->id,
                    'patient_id' => $patient->id,
                    'appointment_date' => now()->addDays(rand(1, 30)),
                ]);

                $assignedPatients[] = $patient->id;
            }
        }
    }
}
