<?php

use App\Http\Controllers\Admin\SubscriptionPlanController;
use App\Http\Controllers\ProfileController;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $plans = SubscriptionPlan::active()->ordered()->get();

    return view('welcome', compact('plans'));
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Grup khusus admin — paket harga dsb ada di sini
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('paket-harga', SubscriptionPlanController::class)
            ->parameters(['paket-harga' => 'paketHarga']);
    });
});

require __DIR__.'/auth.php';