<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StorefrontController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ToolCategoryController;
use App\Http\Controllers\Admin\ToolController;
use App\Http\Controllers\Admin\ToolAccountController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\PackageCustomFieldController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\OtpController as AdminOtpController;
use App\Http\Controllers\Admin\DeviceController as AdminDeviceController;
use App\Http\Controllers\Admin\SupportController as AdminSupportController;
use App\Http\Controllers\Admin\AdminTaskController;
use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Customer\OrderController as CustomerOrderController;
use App\Http\Controllers\Customer\ReviewController as CustomerReviewController;
use App\Http\Controllers\Customer\OtpController as CustomerOtpController;
use App\Http\Controllers\Customer\DeviceController as CustomerDeviceController;
use App\Http\Controllers\Customer\SupportController as CustomerSupportController;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant Routes (each business, resolved by subdomain)
|--------------------------------------------------------------------------
*/

Route::middleware([
    'web',
    InitializeTenancyBySubdomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {

    Route::get('/', [HomeController::class, 'index'])->name('home');

    Route::get('/store', [StorefrontController::class, 'index'])->name('store.index');
    Route::get('/store/{package}', [StorefrontController::class, 'show'])->name('store.show');

    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
        Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
        Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1');
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

            Route::post('/otp/{otpRequest}/provide', [AdminOtpController::class, 'provide'])->name('otp.provide');
            Route::post('/devices/{device}/approve', [AdminDeviceController::class, 'approve'])->name('devices.approve');
            Route::post('/device-resets/{request}/complete', [AdminDeviceController::class, 'completeReset'])->name('device-resets.complete');

            Route::get('/support', [AdminSupportController::class, 'index'])->name('support.index');
            Route::get('/support/{ticket}', [AdminSupportController::class, 'show'])->name('support.show');
            Route::post('/support/{ticket}/reply', [AdminSupportController::class, 'reply'])->name('support.reply');
            Route::post('/support/{ticket}/close', [AdminSupportController::class, 'close'])->name('support.close');

            Route::get('/tasks', [AdminTaskController::class, 'index'])->name('tasks.index');
            Route::get('/tasks/{task}', [AdminTaskController::class, 'show'])->name('tasks.show');
            Route::post('/tasks/{task}/complete', [AdminTaskController::class, 'complete'])->name('tasks.complete');

            Route::post('/announcements', [AnnouncementController::class, 'store'])->name('announcements.store');

            Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');
            Route::post('/reviews/{review}/moderate', [ReviewController::class, 'moderate'])->name('reviews.moderate');
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
            Route::post('/orders/{order}/coupon', [CustomerOrderController::class, 'applyCoupon'])->name('orders.coupon');
            Route::post('/orders/{order}/renew', [CustomerOrderController::class, 'renew'])->name('orders.renew');
            Route::post('/reviews', [CustomerReviewController::class, 'store'])->name('reviews.store');

            Route::post('/otp/request', [CustomerOtpController::class, 'request'])->name('otp.request');
            Route::get('/otp/{otpRequest}', [CustomerOtpController::class, 'show'])->name('otp.show');

            Route::post('/devices/reset', [CustomerDeviceController::class, 'requestReset'])->name('devices.reset');

            Route::get('/support', [CustomerSupportController::class, 'index'])->name('support.index');
            Route::get('/support/{ticket}', [CustomerSupportController::class, 'show'])->name('support.show');
            Route::post('/support', [CustomerSupportController::class, 'store'])->name('support.store');
            Route::post('/support/{ticket}/reply', [CustomerSupportController::class, 'reply'])->name('support.reply');
        });
    });
});
