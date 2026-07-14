<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ToolCategoryController;
use App\Http\Controllers\Admin\ToolController;
use App\Http\Controllers\Admin\ToolAccountController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\PackageCustomFieldController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [DashboardController::class, 'admin'])->name('dashboard');

        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');

        Route::resource('categories', ToolCategoryController::class)->except(['show']);
        Route::resource('tools', ToolController::class)->except(['show']);
        Route::resource('tool-accounts', ToolAccountController::class)->except(['show']);
        Route::resource('packages', PackageController::class)->except(['show']);
        Route::post('/packages/{package}/custom-fields', [PackageCustomFieldController::class, 'store'])->name('packages.custom-fields.store');
        Route::delete('/packages/{package}/custom-fields/{field}', [PackageCustomFieldController::class, 'destroy'])->name('packages.custom-fields.destroy');
    });

    Route::middleware('role:staff')->prefix('staff')->name('staff.')->group(function () {
        Route::get('/', [DashboardController::class, 'staff'])->name('dashboard');
    });

    Route::middleware('role:customer')->prefix('customer')->name('customer.')->group(function () {
        Route::get('/', [DashboardController::class, 'customer'])->name('dashboard');
    });
});
