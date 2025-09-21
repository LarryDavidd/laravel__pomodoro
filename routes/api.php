<?php

use App\Http\Controllers\TodoController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProtectedController;

// Public routes
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:api')->group(function () {
    // Auth routes
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::get('me', [AuthController::class, 'me']);
    
    // Protected data routes
    Route::get('dashboard', [ProtectedController::class, 'dashboard']);
    Route::get('profile', [ProtectedController::class, 'profile']);
    Route::put('profile', [ProtectedController::class, 'updateProfile']);
    
    // Example resource routes
    Route::apiResource('posts', \App\Http\Controllers\PostController::class);
});

// Health check
Route::get('health', function () {
    return response()->json(['status' => 'OK']);
});

Route::middleware('auth:api')->group(function () {
    Route::apiResource('todos', TodoController::class);
});