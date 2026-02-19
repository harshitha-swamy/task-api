<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Task\TaskController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::prefix('v1')->group(function () {

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login',    [AuthController::class, 'login']);

    // Protected routes
    Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {

        // Auth
        Route::post('/logout', [AuthController::class, 'logout']);

        // Dashboard
        Route::get('/dashboard', [TaskController::class, 'dashboard']);

        // Tasks
        Route::apiResource('tasks', TaskController::class);
    });
});