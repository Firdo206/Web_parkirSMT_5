<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BerandaController;
use App\Http\Controllers\Api\ParkingGateController;
use App\Http\Controllers\Api\PasswordController;
use App\Http\Controllers\AccessController;
use App\Http\Controllers\Api\RiwayatController;
use App\Http\Controllers\Api\NotifikasiController;
use App\Http\Middleware\EnsureMemberCanAccess;

// ===== Gerbang parkir (IoT) - punyamu, tidak diubah =====
Route::post('/parking/trigger',[ParkingGateController::class,'trigger']);
Route::get('/parking/command',[ParkingGateController::class,'command']);
Route::post('/parking/reset',[ParkingGateController::class,'reset']);
Route::get('/parking/verification-status',[ParkingGateController::class,'verificationStatus']);
Route::post('/parking/detect-access',[AccessController::class,'detect']);
Route::post('/parking/face-result',[ParkingGateController::class,'faceResult']);
Route::post('/parking/plate-result',[ParkingGateController::class,'plateResult']);

// ===== Aplikasi mobile member =====
// Maksimal 6 percobaan login per menit per IP, mencegah tebak sandi.
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1');

Route::middleware(['auth:sanctum', EnsureMemberCanAccess::class])->group(function () {
    // Tiga rute ini tetap boleh dipakai walau must_change_password masih aktif
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/password/change', [PasswordController::class, 'update']);

    // Rute di bawah ini otomatis terkunci sampai sandi diganti
    Route::get('/beranda', [BerandaController::class, 'index']);
    Route::get('/riwayat', [RiwayatController::class, 'index']);
    Route::get('/notifikasi', [NotifikasiController::class, 'index']);
    Route::post('/notifikasi/{accessRequest}/respon', [NotifikasiController::class, 'respond']);

    // nanti: /riwayat, /alerts, /alerts/{id}/konfirmasi, /panggil-petugas
});