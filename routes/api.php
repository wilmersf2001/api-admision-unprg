<?php

use App\Http\Controllers\AcademicGroupController;
use App\Http\Controllers\AcademicProgramController;
use App\Http\Controllers\AddressTypeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DistributionVacanciesController;
use App\Http\Controllers\DistrictController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\FacultyController;
use App\Http\Controllers\GenderController;
use App\Http\Controllers\ModalityController;
use App\Http\Controllers\PostulantController;
use App\Http\Controllers\PostulantStateController;
use App\Http\Controllers\ProcessController;
use App\Http\Controllers\ProvinceController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\SedeController;
use App\Http\Controllers\SendMailController;
use App\Http\Controllers\TxtFileController;
use App\Http\Controllers\UniversityController;
use App\Http\Controllers\UserController;

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

// Rutas públicas (sin autenticación)
Route::post('/auth/login', [AuthController::class, 'login']);
Route::get('modalities', [ModalityController::class, 'index']);
Route::get('departments', [DepartmentController::class, 'index']);
Route::get('provinces', [ProvinceController::class, 'index']);
Route::get('districts', [DistrictController::class, 'index']);
Route::get('schools', [SchoolController::class, 'index']);
Route::post('banks/verify-payment', [BankController::class, 'VerifyPayment']);

// Ruta pública para registro de postulantes (requiere token de inscripción)
Route::post('postulants', [PostulantController::class, 'store']);
Route::post('postulants/check-registration', [PostulantController::class, 'checkRegistration']);
Route::post('postulants/rectify-files', [PostulantController::class, 'rectifyFiles']);

// Rutas protegidas (con autenticación)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // Routes Users
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'store']);
        Route::get('/{user}', [UserController::class, 'show']);
        Route::put('/{user}', [UserController::class, 'update']);
        Route::delete('/{user}', [UserController::class, 'destroy']);
    });

    // Routes Exam
    Route::prefix('exams')->group(function () {
        Route::get('/', [ExamController::class, 'index']);
        Route::post('/', [ExamController::class, 'store']);
        Route::get('/{exam}', [ExamController::class, 'show']);
        Route::put('/{exam}', [ExamController::class, 'update']);
        Route::delete('/{exam}', [ExamController::class, 'destroy']);
    });

    // Routes Modality
    Route::prefix('modalities')->group(function () {
        Route::post('/', [ModalityController::class, 'store']);
        Route::get('/{modality}', [ModalityController::class, 'show']);
        Route::put('/{modality}', [ModalityController::class, 'update']);
        Route::delete('/{modality}', [ModalityController::class, 'destroy']);
    });

    // Routes Sede
    Route::prefix('sedes')->group(function () {
        Route::get('/', [SedeController::class, 'index']);
        Route::post('/', [SedeController::class, 'store']);
        Route::get('/{sede}', [SedeController::class, 'show']);
        Route::put('/{sede}', [SedeController::class, 'update']);
        Route::delete('/{sede}', [SedeController::class, 'destroy']);
    });

    // Routes University
    Route::prefix('universities')->group(function () {
        Route::get('/', [UniversityController::class, 'index']);
        Route::post('/', [UniversityController::class, 'store']);
        Route::get('/{university}', [UniversityController::class, 'show']);
        Route::put('/{university}', [UniversityController::class, 'update']);
        Route::delete('/{university}', [UniversityController::class, 'destroy']);
    });

    // Route Postulant States
    Route::prefix('postulant-states')->group(function () {
        Route::get('/', [PostulantStateController::class, 'index']);
        Route::post('/', [PostulantStateController::class, 'store']);
        Route::get('/{postulantState}', [PostulantStateController::class, 'show']);
        Route::put('/{postulantState}', [PostulantStateController::class, 'update']);
        Route::delete('/{postulantState}', [PostulantStateController::class, 'destroy']);
    });

    // Route Districts
    Route::prefix('districts')->group(function () {
        Route::post('/', [DistrictController::class, 'store']);
        Route::get('/{district}', [DistrictController::class, 'show']);
        Route::put('/{district}', [DistrictController::class, 'update']);
        Route::delete('/{district}', [DistrictController::class, 'destroy']);
    });

    // Route Genders
    Route::prefix('genders')->group(function () {
        Route::get('/', [GenderController::class, 'index']);
        Route::post('/', [GenderController::class, 'store']);
        Route::get('/{gender}', [GenderController::class, 'show']);
        Route::put('/{gender}', [GenderController::class, 'update']);
        Route::delete('/{gender}', [GenderController::class, 'destroy']);
    });

    // Route Address Types
    Route::prefix('address-types')->group(function () {
        Route::get('/', [AddressTypeController::class, 'index']);
        Route::post('/', [AddressTypeController::class, 'store']);
        Route::get('/{addressType}', [AddressTypeController::class, 'show']);
        Route::put('/{addressType}', [AddressTypeController::class, 'update']);
        Route::delete('/{addressType}', [AddressTypeController::class, 'destroy']);
    });

    //Route Processes
    Route::prefix('processes')->group(function () {
        Route::get('/', [ProcessController::class, 'index']);
        Route::post('/', [ProcessController::class, 'store']);
        Route::get('/{process}', [ProcessController::class, 'show']);
        Route::put('/{process}', [ProcessController::class, 'update']);
        Route::delete('/{process}', [ProcessController::class, 'destroy']);
    });

    // Route Postulants
    Route::prefix('postulants')->group(function () {
        Route::get('/', [PostulantController::class, 'index']);
        Route::get('/{postulant}/postulant-file', [PostulantController::class, 'getFile']);
        Route::get('/valid-files', [PostulantController::class, 'validFiles']);
        Route::get('/observed-files', [PostulantController::class, 'observedFiles']);
        Route::get('/observed-reiterated-files', [PostulantController::class, 'observedReiteratedFiles']);
        Route::get('/rectified-files', [PostulantController::class, 'rectifiedFiles']);
        Route::post('/{postulant}/copy-to-observed', [PostulantController::class, 'copyFilesToObserved']);
        Route::post('/{postulant}/copy-to-valid', [PostulantController::class, 'copyFilesToValid']);
        Route::post('/{postulant}/copy-to-rectified', [PostulantController::class, 'copyFilesToRectified']);
        Route::get('/{postulant}', [PostulantController::class, 'show']);
        Route::put('/{postulant}', [PostulantController::class, 'update']);
    });

    // Route Schools
    Route::prefix('schools')->group(function () {
        Route::post('/', [SchoolController::class, 'store']);
        Route::get('/{school}', [SchoolController::class, 'show']);
        Route::put('/{school}', [SchoolController::class, 'update']);
        Route::delete('/{school}', [SchoolController::class, 'destroy']);
    });

    // Route Academic Groups
    Route::prefix('academic-groups')->group(function () {
        Route::get('/', [AcademicGroupController::class, 'index']);
        Route::post('/', [AcademicGroupController::class, 'store']);
        Route::get('/{academicGroup}', [AcademicGroupController::class, 'show']);
        Route::put('/{academicGroup}', [AcademicGroupController::class, 'update']);
        Route::delete('/{academicGroup}', [AcademicGroupController::class, 'destroy']);
    });

    // Route Faculties
    Route::prefix('faculties')->group(function () {
        Route::get('/', [FacultyController::class, 'index']);
        Route::post('/', [FacultyController::class, 'store']);
        Route::get('/{faculty}', [FacultyController::class, 'show']);
        Route::put('/{faculty}', [FacultyController::class, 'update']);
        Route::delete('/{faculty}', [FacultyController::class, 'destroy']);
    });

    // Route Academic Programs
    Route::prefix('academic-programs')->group(function () {
        Route::get('/', [AcademicProgramController::class, 'index']);
        Route::post('/', [AcademicProgramController::class, 'store']);
        Route::get('/{academicProgram}', [AcademicProgramController::class, 'show']);
        Route::put('/{academicProgram}', [AcademicProgramController::class, 'update']);
        Route::delete('/{academicProgram}', [AcademicProgramController::class, 'destroy']);
    });

    // Route Txt File
    Route::prefix('txt-file')->group(function () {
        Route::get('/', [TxtFileController::class, 'index']);
        Route::post('/', [TxtFileController::class, 'store']);
    });

    // Route Bank
    Route::prefix('banks')->group(function () {
        Route::get('/', [BankController::class, 'index']);
        Route::get('/payment-report', [BankController::class, 'paymentReport']);
    });

    // Route Countries
    Route::prefix('countries')->group(function () {
        Route::get('/', [CountryController::class, 'index']);
    });

    // Route Send Email
    Route::post("/send-email-postulants", [SendMailController::class, "sendMail"]);

    // Route Distribution Vacancies
    Route::prefix('distribution-vacancies')->group(function () {
        Route::get('/', [DistributionVacanciesController::class, 'index']);
        Route::post('/', [DistributionVacanciesController::class, 'store']);
        Route::post('/upsert', [DistributionVacanciesController::class, 'upsert']);
        Route::get('/{distributionVacancies}', [DistributionVacanciesController::class, 'show']);
        Route::put('/{distributionVacancies}', [DistributionVacanciesController::class, 'update']);
        Route::delete('/{distributionVacancies}', [DistributionVacanciesController::class, 'destroy']);
    });
});
