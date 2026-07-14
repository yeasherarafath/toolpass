<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', fn () => view('admin.dashboard'))->name('dashboard');

        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    });

    Route::middleware('role:staff')->prefix('staff')->name('staff.')->group(function () {
        Route::get('/', fn () => view('staff.dashboard'))->name('dashboard');
    });

    Route::middleware('role:customer')->prefix('customer')->name('customer.')->group(function () {
        Route::get('/', fn () => view('customer.dashboard'))->name('dashboard');
    });
});
