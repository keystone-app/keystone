<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\VisitController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::get('/me', [AuthController::class, 'me']);

Route::middleware('auth')->group(function () {
    Route::post('/identity-upload', [DocumentController::class, 'uploadIdentity']);
    Route::get('/visits', [VisitController::class, 'index']);
    Route::post('/visits', [VisitController::class, 'store']);
    Route::patch('/visits/{visit}', [VisitController::class, 'update']);
});
