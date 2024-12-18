<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return 'Novellection';
});

Route::post('/v1/register', [UserController::class, 'register']);
Route::post('/v1/login', [UserController::class, 'login']);
Route::post('/v1/logout', [UserController::class, 'logout'])->middleware('auth:sanctum');

// Route::get('/v1/users', [UserController::class, 'index']);
Route::get('/v1/users', [UserController::class, 'index'])->middleware('auth:sanctum');
