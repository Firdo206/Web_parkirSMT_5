<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ParkingGateController;
use App\Http\Controllers\AccessController;

// ===== Gerbang parkir (IoT) - punyamu, tidak diubah =====
Route::post('/parking/trigger',[ParkingGateController::class,'trigger']);
Route::get('/parking/command',[ParkingGateController::class,'command']);
Route::post('/parking/reset',[ParkingGateController::class,'reset']);
Route::get('/parking/verification-status',[ParkingGateController::class,'verificationStatus']);
Route::post('/parking/detect-access',[AccessController::class,'detect']);
Route::post('/parking/face-result',[ParkingGateController::class,'faceResult']);
Route::post('/parking/plate-result',[ParkingGateController::class,'plateResult']);

// ===== Aplikasi mobile member =====
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
});