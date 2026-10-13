<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

require __DIR__.'/auth.php';
require __DIR__.'/public.php';
require __DIR__.'/webhooks.php';

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    require __DIR__.'/student.php';
});

Route::middleware(['auth', 'verified', 'role:instructor|admin'])
    ->prefix('instruktur')
    ->name('instructor.')
    ->group(function (): void {
        require __DIR__.'/instructor.php';
    });

Route::middleware(['auth', 'verified', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        require __DIR__.'/admin.php';
    });
