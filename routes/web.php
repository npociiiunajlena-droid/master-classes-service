<?php

declare(strict_types=1);

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CabinetController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MasterClassController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/categories/{creativityType}', CategoryController::class)->name('categories.show');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('auth.login');
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login.attempt');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('auth.register');
    Route::post('/register', [AuthController::class, 'register'])->name('auth.register.store');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('auth.logout');

Route::middleware(['auth', 'role:master'])->group(function (): void {
    Route::get('/cabinet', CabinetController::class)->name('cabinet.index');
    Route::get('/master-classes/create', [MasterClassController::class, 'create'])->name('master-classes.create');
    Route::post('/master-classes', [MasterClassController::class, 'store'])->name('master-classes.store');
    Route::get('/master-classes/{masterClass}/edit', [MasterClassController::class, 'edit'])->name('master-classes.edit');
    Route::patch('/master-classes/{masterClass}', [MasterClassController::class, 'update'])->name('master-classes.update');
});

Route::middleware(['auth', 'role:visitor'])->group(function (): void {
    Route::get('/enrollments', [EnrollmentController::class, 'index'])->name('enrollments.index');
    Route::delete('/enrollments/{masterClass}', [EnrollmentController::class, 'destroy'])->name('enrollments.destroy');
    Route::get('/my-enrollments', fn () => redirect()->route('enrollments.index'));
});

Route::middleware('auth')->group(function (): void {
    Route::get('/enrollments/{masterClass}/confirm', [EnrollmentController::class, 'confirm'])->name('enrollments.confirm');
    Route::post('/enrollments/{masterClass}', [EnrollmentController::class, 'store'])->name('enrollments.store');
    Route::post('/enrollments/{masterClass}/cancel', [EnrollmentController::class, 'cancel'])->name('enrollments.cancel');
});
