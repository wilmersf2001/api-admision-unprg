<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\ModalityController;
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
        Route::get('/{user}', [ExamController::class, 'show']);
        Route::put('/{user}', [ExamController::class, 'update']);
        Route::delete('/{user}', [ExamController::class, 'destroy']);
    });

    // Routes Modality
    Route::prefix('modalities')->group(function () {
        Route::get('/', [ModalityController::class, 'index']);
        Route::post('/', [ModalityController::class, 'store']);
        Route::get('/{modality}', [ModalityController::class, 'show']);
        Route::put('/{modality}', [ModalityController::class, 'update']);
        Route::delete('/{modality}', [ModalityController::class, 'destroy']);
    });
});
