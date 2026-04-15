<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PatientAppointmentController;
use App\Http\Controllers\Api\PatientController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('get-patient-appointments',[PatientAppointmentController::class,'getPatientAppointments']);
Route::get('get-appointment-departments',[PatientAppointmentController::class, 'getAppointmentDepartments']);
Route::get('get-department-speciality-with-physician',[PatientAppointmentController::class, 'getDepartmentSpecialityWithPhysician']);
Route::get('get-patient-details',[PatientController::class, 'getPatientDetails']);