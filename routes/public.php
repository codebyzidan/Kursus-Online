<?php

use App\Http\Controllers\Public\CatalogController;
use App\Http\Controllers\Public\CertificateVerificationController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\InstructorProfileController;
use App\Http\Controllers\Public\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/katalog', [CatalogController::class, 'index'])->name('courses.index');
Route::get('/kursus/{course}', [CatalogController::class, 'show'])->name('courses.show');

Route::get('/pengajar/{instructor}', [InstructorProfileController::class, 'show'])->name('instructors.show');

Route::get('/sertifikat/verifikasi', [CertificateVerificationController::class, 'form'])->name('certificates.verify.form');
Route::post('/sertifikat/verifikasi', [CertificateVerificationController::class, 'verify'])
    ->middleware('throttle:certificate-verify')
    ->name('certificates.verify');

Route::get('/kebijakan-privasi', [PageController::class, 'privacy'])->name('pages.privacy');
Route::get('/syarat-layanan', [PageController::class, 'terms'])->name('pages.terms');
