<?php

use App\Http\Controllers\Admin\SubscriptionPlanController;
use App\Http\Controllers\adminParkir\DaruratController;
use App\Http\Controllers\adminParkir\DashboardController as ParkirDashboardController;
use App\Http\Controllers\adminParkir\PelangganController;
use App\Http\Controllers\adminParkir\PendaftaranController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SuperAdmin\AdminAccountController;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $plans = SubscriptionPlan::active()->ordered()->get();

    return view('welcome', compact('plans'));
});

// Dashboard Admin Web. Role lain dilempar ke panelnya masing-masing.
Route::get('/dashboard', function () {
    $user = auth()->user();

    return match ($user->role) {
        User::ROLE_SUPERADMIN   => redirect()->route('superadmin.admins.index'),
        User::ROLE_ADMIN_PARKIR => redirect()->route('parkir.dashboard'),
        User::ROLE_ADMIN_WEB    => view('dashboard'),
        default                 => abort(403),
    };
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin Web: paket harga dsb
Route::middleware(['auth', 'role:admin_web,superadmin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::resource('paket-harga', SubscriptionPlanController::class)
            ->parameters(['paket-harga' => 'paketHarga']);
    });

// Admin Parkir: operasional
Route::middleware(['auth', 'role:admin_parkir'])
    ->prefix('parkir')
    ->name('parkir.')
    ->group(function () {
        Route::get('/', [ParkirDashboardController::class, 'index'])->name('dashboard');

        Route::get('/pendaftaran', [PendaftaranController::class, 'index'])->name('pendaftaran');
        Route::get('/pelanggan', [PelangganController::class, 'index'])->name('pelanggan');
        Route::get('/darurat', [DaruratController::class, 'index'])->name('darurat');
        // nanti: members, kendaraan, wajah, log akses, palang manual
    });

// Superadmin: kelola akun admin
Route::middleware(['auth', 'role:superadmin'])
    ->prefix('superadmin')
    ->name('superadmin.')
    ->group(function () {
        Route::resource('admins', AdminAccountController::class)->except(['show']);
        Route::patch('admins/{admin}/toggle', [AdminAccountController::class, 'toggle'])->name('admins.toggle');
        Route::patch('admins/{admin}/reset-password', [AdminAccountController::class, 'resetPassword'])->name('admins.reset');
    });

require __DIR__.'/auth.php';