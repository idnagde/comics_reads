<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::get('/', function () {
    return 'Yu Comics Read';
});

Route::post('/v1/register', [UserController::class, 'register']);
Route::post('/v1/login', [UserController::class, 'login']);
Route::get('/v1/users', [UserController::class, 'index']);
