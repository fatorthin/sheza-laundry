<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ReceiptController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\LandingController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/profil', [LandingController::class, 'profil'])->name('profil');

// Auth routes (manual)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Admin (protected)
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // POS
    Route::get('/pos', [OrderController::class, 'pos'])->name('pos');

    // Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('orders');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');
    Route::post('/orders/{order}/weight', [OrderController::class, 'finalizeWeight'])->name('orders.weight');

    // Receipt print
    Route::get('/orders/{order}/receipt', [ReceiptController::class, 'show'])->name('receipt');

    // Members
    Route::get('/members', [MemberController::class, 'index'])->name('members');
    Route::post('/members', [MemberController::class, 'store'])->name('members.store');
    Route::put('/members/{member}', [MemberController::class, 'update'])->name('members.update');
    Route::delete('/members/{member}', [MemberController::class, 'destroy'])->name('members.destroy');

    // Services
    Route::get('/services', [ServiceController::class, 'index'])->name('services');
    Route::post('/services', [ServiceController::class, 'store'])->name('services.store');
    Route::put('/services/{service}', [ServiceController::class, 'update'])->name('services.update');
    Route::delete('/services/{service}', [ServiceController::class, 'destroy'])->name('services.destroy');
});

// Redirect /home to admin dashboard
Route::get('/home', fn() => redirect()->route('admin.dashboard'));
