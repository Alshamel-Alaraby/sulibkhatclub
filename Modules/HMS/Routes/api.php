<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('h_m_s')->group(function () {
    Route::get('dashboard', 'HMSDashboardController@index');
    Route::apiResource('drugs', "HMSDrugController");
    Route::post("drugs/bulk-delete", "HMSDrugController@bulkDelete");

    Route::apiResource('diagnosis_tests', "HMSDiagnosisTestController");
    Route::post("diagnosis_tests/bulk-delete", "HMSDiagnosisTestController@bulkDelete");
    Route::get('diagnosis_tests/logs/{id}', 'HMSDiagnosisTestController@logs');


    Route::apiResource('specialties', "HMSSpecialtyController");
    Route::post("specialties/bulk-delete", "HMSSpecialtyController@bulkDelete");
    Route::get('specialties/logs/{id}', 'HMSSpecialtyController@logs');


    Route::apiResource('service_types', "HMSServiceTypeController");
    Route::post("service_types/bulk-delete", "HMSServiceTypeController@bulkDelete");

    Route::apiResource('rooms', "HMSRoomController");
    Route::post("rooms/bulk-delete", "HMSRoomController@bulkDelete");
    Route::get('rooms/logs/{id}', 'HMSRoomController@logs');

    Route::apiResource('rooms_categories', "HMSRoomsCategoryController");
    Route::get('opreations_rooms_categories', "HMSRoomsCategoryController@opreations_rooms_categories");
    Route::apiResource('days', "HMSDayController")->only(['index']);
    Route::post("rooms_categories/bulk-delete", "HMSRoomsCategoryController@bulkDelete");
    Route::get('rooms_categories/logs/{id}', 'HMSRoomsCategoryController@logs');

    Route::apiResource('days', "HMSDayController")->only(['index']);
//    Route::get('days/logs/{id}', 'HMSDayController@logs');


    Route::apiResource('insurance_companies', "HMSInsuranceCompanyController");
    Route::post("insurance_companies/bulk-delete", "HMSInsuranceCompanyController@bulkDelete");
    Route::get('insurance_companies/logs/{id}', 'HMSInsuranceCompanyController@logs');


    Route::apiResource('patients', "HMSPatientController");
    Route::post("patients/bulk-delete", "HMSPatientController@bulkDelete");
    Route::get('patients/logs/{id}', 'HMSPatientController@logs');
    Route::get('get_patient_data/{patient}', "HMSPatientController@get_patient_data");

    Route::apiResource('patient_health_histories', "HMSPatientHealthHistoryController");
    Route::post("patient_health_histories/bulk-delete", "HMSPatientHealthHistoryController@bulkDelete");
    Route::get('patient_health_histories/logs/{id}', 'HMSPatientHealthHistoryController@logs');


    Route::apiResource('patient_medical_files', "HMSPatientMedicalFileController");
    Route::post("patient_medical_files/bulk-delete", "HMSPatientMedicalFileController@bulkDelete");
    Route::get('patient_medical_files/logs/{id}', 'HMSPatientMedicalFileController@logs');

    Route::apiResource('doctors', "HMSDoctorController");
    Route::apiResource('settings', "HMSSettingController");
    Route::post("doctors/bulk-delete", "HMSDoctorController@bulkDelete");
    Route::get('doctors/logs/{id}', 'HMSDoctorController@logs');

    Route::apiResource('prescriptions', "HMSPrescriptionController");
    Route::post("prescriptions/bulk-delete", "HMSPrescriptionController@bulkDelete");
    Route::get('prescriptions/logs/{id}', 'HMSPrescriptionController@logs');


    Route::apiResource('appointments', "HMSAppointmentController");
    Route::post("appointments/bulk-delete", "HMSAppointmentController@bulkDelete");
    Route::get('doctor_available_times', "HMSAppointmentController@available_times");
    Route::get('get_next_patient_for_each_doctor', "HMSAppointmentController@get_next_patient_for_each_doctor");
    Route::get('appointments/logs/{id}', 'HMSAppointmentController@logs');

    Route::post('next_patient', "HMSAppointmentController@next_patient");
    Route::post('change_appointment_status/{appointment}', "HMSAppointmentController@change_appoinment_status");

    Route::apiResource('rooms_reservation', "HMSRoomReservationAppointmentController");
    Route::get('room_available_times', "HMSRoomReservationAppointmentController@available_times");

});
