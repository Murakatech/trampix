<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FreelancerDashboardController;
use App\Http\Controllers\CompanyDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/freelancer/dashboard', FreelancerDashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('freelancer.dashboard');

Route::get('/company/dashboard', CompanyDashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('company.dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
