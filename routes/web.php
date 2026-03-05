<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\LeaseController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\VisitController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::get('/me', [AuthController::class, 'me']);

Route::get('/properties', [PropertyController::class, 'index']);

Route::middleware('auth')->group(function () {
    Route::post('/identity-upload', [DocumentController::class, 'uploadIdentity']);
    Route::post('/compliance-upload', [DocumentController::class, 'uploadCompliance']);
    Route::get('/visits', [VisitController::class, 'index']);
    Route::get('/my-visits', [VisitController::class, 'myVisits']);
    Route::post('/visits', [VisitController::class, 'store']);
    Route::patch('/visits/{visit}', [VisitController::class, 'update']);

    Route::post('/properties', [PropertyController::class, 'store']);

    Route::get('/offers', [OfferController::class, 'index']);
    Route::post('/offers', [OfferController::class, 'store']);
    Route::patch('/offers/{offer}', [OfferController::class, 'update']);
    Route::post('/offers/{offer}/verify', [OfferController::class, 'verify']);

    Route::get('/leases', [LeaseController::class, 'index']);
    Route::post('/leases/{lease}/upload', [LeaseController::class, 'uploadDocument']);
    Route::post('/leases/{lease}/sign', [LeaseController::class, 'sign']);

    Route::get('/maintenance', [\App\Http\Controllers\MaintenanceController::class, 'index']);
    Route::post('/maintenance', [\App\Http\Controllers\MaintenanceController::class, 'store']);
    Route::patch('/maintenance/{maintenanceRequest}', [\App\Http\Controllers\MaintenanceController::class, 'update']);
});
