<?php

use App\Http\Controllers\AddressTypeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DistrictController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\GenderController;
use App\Http\Controllers\ModalityController;
use App\Http\Controllers\PostulantStateController;
use App\Http\Controllers\ProcessController;
use App\Http\Controllers\ProvinceController;
use App\Http\Controllers\SedeController;
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
        Route::get('/', [ModalityController::class, 'index']);
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

    // Route Departments
    Route::prefix('departments')->group(function () {
        Route::get('/', [DepartmentController::class, 'index']);
    });

    // Route Provinces
    Route::prefix('provinces')->group(function () {
        Route::get('/', [ProvinceController::class, 'index']);
    });

    // Route Districts
    Route::prefix('districts')->group(function () {
        Route::get('/', [DistrictController::class, 'index']);
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
});
