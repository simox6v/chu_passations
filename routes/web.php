<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\PassationController;
use App\Http\Controllers\SalleController;

Route::get('/', fn() => redirect('/login'));

// ✅ Dashboard route now uses PassationController method for logic
Route::get('/dashboard', [PassationController::class, 'dashboard'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// ✅ Profile management routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ✅ Admin-only routes (prefix admin + name admin.*)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('users', UserController::class);
    Route::resource('salles', SalleController::class);
    Route::put('/users/{user}/password', [UserController::class, 'changePassword'])->name('users.changePassword');
});

// ✅ Passation routes for all authenticated users
Route::middleware(['auth'])->group(function () {
    Route::resource('passations', PassationController::class);
    Route::delete('/passations/{passation}', [PassationController::class, 'destroy'])->name('passations.destroy');
    Route::get('/passations-search-all', [PassationController::class, 'searchAllPassations'])->name('passations.searchAll');
});



require __DIR__.'/auth.php';
