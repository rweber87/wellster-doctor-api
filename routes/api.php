<?php

use App\Http\Controllers\DoctorController;
use App\Http\Controllers\PatientController;

Route::apiResource('doctors', DoctorController::class)->only('show');
Route::get('/doctors/{doctor}/patients', [DoctorController::class, 'patients']);
Route::get('/doctors/{doctor}/matches', [DoctorController::class, 'matches']);
Route::post('/doctors/{doctor}/assign-patient/{patient}', [DoctorController::class, 'assignPatient']);

Route::get('/unassigned-patients', [PatientController::class, 'unassignedPatients']);
