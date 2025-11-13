<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BicycleController;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::prefix('user')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('user.dashboard');
    Route::get('/bicycles', [UserController::class, 'bicycles'])->name('user.bicycles');
    Route::get('/bicycles/rent/{id}', [UserController::class, 'showRentForm'])->name('user.rent.form');
    Route::post('/bicycles/rent/{id}', [UserController::class, 'rentBicycle'])->name('user.rent');
    Route::get('/history', [UserController::class, 'history'])->name('user.history');
    Route::post('/return/{id}', [UserController::class, 'returnBicycle'])->name('user.return');
});

Route::prefix('admin')->middleware(['auth', 'App\Http\Middleware\AdminMiddleware'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/bicycles', [BicycleController::class, 'index'])->name('admin.bicycles');
    Route::post('/bicycles/store', [BicycleController::class, 'store'])->name('admin.bicycles.store');
    Route::post('/bicycles/update/{id}', [BicycleController::class, 'update'])->name('admin.bicycles.update');
    Route::delete('/bicycles/{id}', [BicycleController::class, 'destroy'])->name('admin.bicycles.destroy');
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::get('/payments', [AdminController::class, 'payments'])->name('admin.payments');
    Route::get('/transactions', [AdminController::class, 'transactions'])->name('admin.transactions');
    Route::post('/transactions/{id}/status', [AdminController::class, 'updateTransactionStatus'])->name('admin.transactions.updateStatus');
    Route::get('/reports', [AdminController::class, 'reports'])->name('admin.reports');
});

