<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\MedicalRecordController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\PatientController;

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/appointments', [AppointmentController::class, 'store']);
    Route::get('/appointments', [AppointmentController::class, 'index']);


    Route::post('/medical-records', [MedicalRecordController::class, 'store']);

    Route::middleware('can:view-logs')->get('/audit-logs', [LogController::class, 'index']);

    Route::get('/medical-records/{medicalRecord}', [MedicalRecordController::class, 'show']);
    Route::put('/medical-records/{medicalRecord}', [MedicalRecordController::class, 'update']);
    Route::delete('/medical-records/{medicalRecord}', [MedicalRecordController::class, 'destroy']);

    Route::get('/patients/{id}', [PatientController::class, 'show']);

});


