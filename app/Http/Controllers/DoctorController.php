<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Doctor;
use App\Models\Patient;

class DoctorController extends Controller
{
    public function show(Doctor $doctor)
    {
        return response()->json($doctor->load(['patients', 'assignments', 'indications']));
    }

    public function patients(Doctor $doctor)
    {
        $sortBy = request()->get('sort_by', 'appointment_date');
        $direction = request()->get('direction', 'asc');

        // TODO: Validate direction and sort_by
        // TODO: Use an enum or constant for comparative in sortBy

        $patients = $doctor->patients()->with('assignments')
            ->when($sortBy === 'last_name', function ($query) use ($direction) {
                $query->orderBy('last_name', $direction);
            })
            ->when($sortBy === 'appointment_date', function ($query) use ($direction) {
                $query->orderBy(
                    Assignment::select('appointment_date')
                        ->whereColumn('assignments.patient_id', 'patients.id'),
                    $direction
                );
            })
            ->get();

        return response()->json($patients->load('indications'));
    }

    public function matches(Doctor $doctor)
    {
        // TODO: Include indications in returned patients

        $indicationIds = $doctor->indications->pluck('id');
        $patients = Patient::doesntHave('assignments')
            ->whereHas('indications', function ($query) use ($indicationIds) {
                $query->whereIn('indications.id', $indicationIds);
            })
            ->get();

        return response()->json($patients->load('indications'));
    }

    public function assignPatient(Doctor $doctor, Patient $patient)
    {
        // TODO: either add endpoint or allow param from client that sets specific date

        $existingAssignment = Assignment::where('patient_id', $patient->id)->exists();

        if ($existingAssignment) {
            return response()->json(['error' => 'This patient is already assigned to a doctor.'], 400);
        }

        $doctorIndications = $doctor->indications->pluck('id')->toArray();
        $patientIndications = $patient->indications->pluck('id')->toArray();
        $commonIndications = array_intersect($doctorIndications, $patientIndications);

        if (empty($commonIndications)) {
            return response()->json(['error' => 'This doctor is not qualified to treat this patient.'], 400);
        }

        Assignment::create([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'appointment_date' => now()->addDays(rand(1, 30)),
        ]);

        return response()->json([
            'message' => 'Patient assigned to doctor successfully.',
            'doctor' => $doctor->load('assignments'),
        ]);
    }
}
