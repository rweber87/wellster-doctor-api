<?php

namespace App\Http\Controllers;

use App\Models\Patient;

class PatientController extends Controller
{
    public function unassignedPatients()
    {
        $patients = Patient::doesntHave('assignments')->get();

        return response()->json($patients);
    }
}
