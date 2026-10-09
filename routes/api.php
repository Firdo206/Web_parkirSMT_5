<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ParkingGateController;
use App\Http\Controllers\AccessController;

Route::post('/parking/trigger',[ParkingGateController::class,'trigger']);
Route::get('/parking/command',[ParkingGateController::class,'command']);
Route::post('/parking/reset',[ParkingGateController::class,'reset']);
Route::get('/parking/verification-status',[ParkingGateController::class,'verificationStatus']);
Route::post('/parking/detect-access',[AccessController::class,'detect']);
Route::post('/parking/face-result',[ParkingGateController::class,'faceResult']);
Route::post('/parking/plate-result',[ParkingGateController::class,'plateResult']);