<?php

use App\Http\Controllers\NovelController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return 'Novellection';
});

Route::post('/v1/register', [UserController::class, 'register']);
Route::post('/v1/login', [UserController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/v1/logout', [UserController::class, 'logout']);
    Route::get('/v1/users', [UserController::class, 'index']);
    Route::get('/v1/users/{id}', [UserController::class, 'show']);
    Route::post('/v1/register-as-author', [UserController::class, 'registerAsAuthor']);
    
    Route::post('/v1/novels', [NovelController::class, 'store']);
});