<?php

use App\Http\Controllers\Student\CertificateController;
use App\Http\Controllers\Student\CheckoutController;
use App\Http\Controllers\Student\InstructorApplicationController;
use App\Http\Controllers\Student\LearningController;
use App\Http\Controllers\Student\OrderController;
use App\Http\Controllers\Student\QuizController;
use App\Http\Controllers\Student\ReviewController;
use App\Http\Controllers\Student\WishlistController;
use Illuminate\Support\Facades\Route;

Route::get('kursus-saya', [LearningController::class, 'index'])->name('student.courses.index');

Route::get('belajar/{course}', [LearningController::class, 'show'])->name('student.learning.show');
Route::get('belajar/{course}/materi/{lesson}', [LearningController::class, 'lesson'])->name('student.learning.lesson');
Route::post('belajar/{course}/materi/{lesson}/progres', [LearningController::class, 'progress'])->name('student.learning.progress');

Route::get('belajar/{course}/kuis', [LearningController::class, 'quizzes'])->name('student.quizzes.index');
Route::get('belajar/{course}/kuis/{quiz}', [QuizController::class, 'show'])->name('student.quizzes.show');
Route::post('belajar/{course}/kuis/{quiz}', [QuizController::class, 'submit'])->name('student.quizzes.submit');
Route::get('belajar/{course}/kuis/{quiz}/hasil/{attempt}', [QuizController::class, 'result'])->name('student.quizzes.result');

Route::get('wishlist', [WishlistController::class, 'index'])->name('student.wishlist.index');
Route::post('wishlist/{course}', [WishlistController::class, 'store'])->name('student.wishlist.store');
Route::delete('wishlist/{course}', [WishlistController::class, 'destroy'])->name('student.wishlist.destroy');

Route::get('pesanan', [OrderController::class, 'index'])->name('student.orders.index');
Route::get('pesanan/{order}', [OrderController::class, 'show'])->name('student.orders.show');
Route::get('pesanan/{order}/invoice', [OrderController::class, 'invoice'])->name('student.orders.invoice');

Route::get('sertifikat', [CertificateController::class, 'index'])->name('student.certificates.index');
Route::get('sertifikat/{certificate}/unduh', [CertificateController::class, 'download'])->name('student.certificates.download');

Route::post('kursus/{course}/ulasan', [ReviewController::class, 'store'])->name('student.reviews.store');

Route::get('checkout', [CheckoutController::class, 'create'])->name('checkout.create');
Route::post('checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('checkout/{order}', [CheckoutController::class, 'show'])->name('checkout.show');
Route::get('checkout/{order}/selesai', [CheckoutController::class, 'success'])->name('checkout.success');

Route::get('pengajuan-instruktur', [InstructorApplicationController::class, 'create'])->name('student.instructor-application.create');
Route::post('pengajuan-instruktur', [InstructorApplicationController::class, 'store'])->name('student.instructor-application.store');
