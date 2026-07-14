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
use App\Http\Controllers\StorefrontController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Customer\OrderController as CustomerOrderController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/store', [StorefrontController::class, 'index'])->name('store.index');
Route::get('/store/{package}', [StorefrontController::class, 'show'])->name('store.show');

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

        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::post('/orders/{order}/review-info', [OrderController::class, 'reviewInfo'])->name('orders.review-info');
        Route::post('/payments/{payment}/verify', [OrderController::class, 'verifyPayment'])->name('payments.verify');
        Route::post('/payments/{payment}/reject', [OrderController::class, 'rejectPayment'])->name('payments.reject');
        Route::post('/accesses/{access}/deliver', [OrderController::class, 'deliverAccess'])->name('accesses.deliver');
    });

    Route::middleware('role:staff')->prefix('staff')->name('staff.')->group(function () {
        Route::get('/', [DashboardController::class, 'staff'])->name('dashboard');
    });

    Route::middleware('role:customer')->prefix('customer')->name('customer.')->group(function () {
        Route::get('/', [DashboardController::class, 'customer'])->name('dashboard');

        Route::get('/orders', [CustomerOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [CustomerOrderController::class, 'show'])->name('orders.show');
        Route::post('/orders', [CustomerOrderController::class, 'store'])->name('orders.store');
        Route::post('/orders/{order}/info', [CustomerOrderController::class, 'submitInfo'])->name('orders.info');
        Route::post('/orders/{order}/payments', [CustomerOrderController::class, 'storePayment'])->name('orders.payments');
    });
});
