<?php

use App\Http\Controllers\TodoController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProtectedController;

// Public routes
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::get('health', function () {
    return response()->json(['status' => 'OK']);
});

// Protected routes
Route::middleware('auth:api')->group(function () {
    // Auth
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::get('me', [AuthController::class, 'me']);
    
    // User
    Route::get('dashboard', [ProtectedController::class, 'dashboard']);
    Route::get('profile', [ProtectedController::class, 'profile']);
    Route::put('profile', [ProtectedController::class, 'updateProfile']);
    
    // Todos
    Route::apiResource('todos', TodoController::class);
    Route::get('todo-stats', [TodoController::class, 'stats']);
});