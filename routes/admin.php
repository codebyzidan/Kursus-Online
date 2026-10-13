<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InstructorApplicationController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', DashboardController::class)->name('dashboard');

Route::get('pengguna', [UserController::class, 'index'])->name('users.index');
Route::patch('pengguna/{user}', [UserController::class, 'update'])->name('users.update');
Route::delete('pengguna/{user}', [UserController::class, 'destroy'])->name('users.destroy');

Route::get('pengajuan-instruktur', [InstructorApplicationController::class, 'index'])->name('instructor-applications.index');
Route::post('pengajuan-instruktur/{application}/setujui', [InstructorApplicationController::class, 'approve'])->name('instructor-applications.approve');
Route::post('pengajuan-instruktur/{application}/tolak', [InstructorApplicationController::class, 'reject'])->name('instructor-applications.reject');

Route::get('kategori', [CategoryController::class, 'index'])->name('categories.index');
Route::post('kategori', [CategoryController::class, 'store'])->name('categories.store');
Route::patch('kategori/{category}', [CategoryController::class, 'update'])->name('categories.update');
Route::delete('kategori/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

Route::get('kursus', [CourseController::class, 'index'])->name('courses.index');
Route::get('kursus/{course}', [CourseController::class, 'show'])->name('courses.show');
Route::post('kursus/{course}/setujui', [CourseController::class, 'approve'])->name('courses.approve');
Route::post('kursus/{course}/tolak', [CourseController::class, 'reject'])->name('courses.reject');

Route::get('pesanan', [OrderController::class, 'index'])->name('orders.index');
Route::get('pesanan/{order}', [OrderController::class, 'show'])->name('orders.show');
Route::post('pesanan/{order}/refund', [OrderController::class, 'refund'])->name('orders.refund');

Route::get('kupon', [CouponController::class, 'index'])->name('coupons.index');
Route::post('kupon', [CouponController::class, 'store'])->name('coupons.store');
Route::patch('kupon/{coupon}', [CouponController::class, 'update'])->name('coupons.update');
Route::delete('kupon/{coupon}', [CouponController::class, 'destroy'])->name('coupons.destroy');

Route::get('ulasan', [ReviewController::class, 'index'])->name('reviews.index');
Route::post('ulasan/{review}/setujui', [ReviewController::class, 'approve'])->name('reviews.approve');
Route::delete('ulasan/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

Route::get('laporan', [ReportController::class, 'index'])->name('reports.index');
Route::get('laporan/ekspor', [ReportController::class, 'export'])->name('reports.export');

Route::get('pengaturan', [SettingController::class, 'index'])->name('settings.index');
Route::patch('pengaturan', [SettingController::class, 'update'])->name('settings.update');
