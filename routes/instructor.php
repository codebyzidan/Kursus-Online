<?php

use App\Http\Controllers\Instructor\CourseController;
use App\Http\Controllers\Instructor\CurriculumController;
use App\Http\Controllers\Instructor\DashboardController;
use App\Http\Controllers\Instructor\EarningsController;
use App\Http\Controllers\Instructor\PayoutController;
use App\Http\Controllers\Instructor\QuizController;
use App\Http\Controllers\Instructor\StudentController;
use Illuminate\Support\Facades\Route;

Route::get('/', DashboardController::class)->name('dashboard');

Route::get('kursus', [CourseController::class, 'index'])->name('courses.index');
Route::get('kursus/baru', [CourseController::class, 'create'])->name('courses.create');
Route::post('kursus', [CourseController::class, 'store'])->name('courses.store');
Route::get('kursus/{course}/edit', [CourseController::class, 'edit'])->name('courses.edit');
Route::patch('kursus/{course}', [CourseController::class, 'update'])->name('courses.update');
Route::delete('kursus/{course}', [CourseController::class, 'destroy'])->name('courses.destroy');
Route::post('kursus/{course}/ajukan', [CourseController::class, 'submit'])->name('courses.submit');

Route::get('kursus/{course}/kurikulum', [CurriculumController::class, 'index'])->name('courses.curriculum');
Route::post('kursus/{course}/bagian', [CurriculumController::class, 'storeSection'])->name('sections.store');
Route::post('kursus/{course}/bagian/urutkan', [CurriculumController::class, 'reorderSections'])->name('sections.reorder');
Route::patch('kursus/{course}/bagian/{section}', [CurriculumController::class, 'updateSection'])->name('sections.update');
Route::delete('kursus/{course}/bagian/{section}', [CurriculumController::class, 'destroySection'])->name('sections.destroy');
Route::post('kursus/{course}/bagian/{section}/materi', [CurriculumController::class, 'storeLesson'])->name('lessons.store');
Route::post('kursus/{course}/bagian/{section}/materi/urutkan', [CurriculumController::class, 'reorderLessons'])->name('lessons.reorder');
Route::patch('kursus/{course}/bagian/{section}/materi/{lesson}', [CurriculumController::class, 'updateLesson'])->name('lessons.update');
Route::delete('kursus/{course}/bagian/{section}/materi/{lesson}', [CurriculumController::class, 'destroyLesson'])->name('lessons.destroy');

Route::get('kuis', [QuizController::class, 'index'])->name('quizzes.index');
Route::get('kuis/baru', [QuizController::class, 'create'])->name('quizzes.create');
Route::post('kuis', [QuizController::class, 'store'])->name('quizzes.store');
Route::get('kursus/{course}/kuis/{quiz}/edit', [QuizController::class, 'edit'])->name('quizzes.edit');
Route::patch('kursus/{course}/kuis/{quiz}', [QuizController::class, 'update'])->name('quizzes.update');
Route::delete('kursus/{course}/kuis/{quiz}', [QuizController::class, 'destroy'])->name('quizzes.destroy');
Route::post('kursus/{course}/kuis/{quiz}/pertanyaan', [QuizController::class, 'storeQuestion'])->name('questions.store');
Route::patch('kursus/{course}/kuis/{quiz}/pertanyaan/{question}', [QuizController::class, 'updateQuestion'])->name('questions.update');
Route::delete('kursus/{course}/kuis/{quiz}/pertanyaan/{question}', [QuizController::class, 'destroyQuestion'])->name('questions.destroy');

Route::get('siswa', [StudentController::class, 'index'])->name('students.index');
Route::get('pendapatan', [EarningsController::class, 'index'])->name('earnings.index');
Route::get('pencairan', [PayoutController::class, 'index'])->name('payouts.index');
Route::post('pencairan', [PayoutController::class, 'store'])->name('payouts.store');
