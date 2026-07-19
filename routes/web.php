<?php

use App\Http\Controllers\Platform\AdminAuthController;
use App\Http\Controllers\Platform\AdminController;
use App\Http\Controllers\Platform\CacheController;
use App\Http\Controllers\Platform\AdminDashboardController;
use App\Http\Controllers\Platform\AdminSubscribeController;
use App\Http\Controllers\Platform\ImpersonationController;
use App\Http\Controllers\Platform\LandingController;
use App\Http\Controllers\Platform\OwnerAuthController;
use App\Http\Controllers\Platform\OwnerController;
use App\Http\Controllers\Platform\OwnerDashboardController;
use App\Http\Controllers\Platform\OwnerSettingsController;
use App\Http\Controllers\Platform\PlanController;
use App\Http\Controllers\Platform\PermissionController;
use App\Http\Controllers\Platform\RoleController;
use App\Http\Controllers\Platform\SettingsController;
use App\Http\Controllers\Platform\SubscriptionController;
use App\Http\Controllers\Platform\TenantController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Central Routes (platform GOD admin + business owner + public landing)
|--------------------------------------------------------------------------
| These routes run on the central domain(s) only. Tenant business routes
| live in routes/tenant.php and are resolved by subdomain. The GOD admin
| panel uses the `admin.*` name prefix and is served under the settings
| driven `admin_path` (default `yatpmin`). The business owner panel uses
| `owner.*` under `owner_path` (default `business`).
*/

foreach (config('tenancy.central_domains') as $domain) {
    Route::domain($domain)->group(function () {

        // Public landing page
        Route::get('/', [LandingController::class, 'index'])->name('landing');

        // ---- Business Owner panel (auth:owner) under owner_path ----
        $ownerPath = app(\App\Services\Settings::class)->path('owner_path', 'business');
        Route::prefix($ownerPath)->name('owner.')->group(function () {
            Route::middleware('guest:owner')->group(function () {
                Route::get('/login', [OwnerAuthController::class, 'showLogin'])->name('login');
                Route::post('/login', [OwnerAuthController::class, 'login'])->middleware('throttle:5,1')->name('login.attempt');

                Route::get('/register', [OwnerAuthController::class, 'showRegister'])->name('register');
                Route::post('/register', [OwnerAuthController::class, 'register'])->middleware('throttle:5,1')->name('register.attempt');
            });

            Route::middleware('auth:owner')->group(function () {
                Route::post('/logout', [OwnerAuthController::class, 'logout'])->name('logout');
                Route::get('/dashboard', [OwnerDashboardController::class, 'index'])->name('dashboard');

                Route::middleware('can:manage-settings')->group(function () {
                    Route::get('/settings', [OwnerSettingsController::class, 'edit'])->name('settings.edit');
                    Route::put('/settings', [OwnerSettingsController::class, 'update'])->name('settings.update');
                });
            });
        });

        // ---- Platform GOD Admin panel (auth:admin) under admin_path ----
        $adminPath = app(\App\Services\Settings::class)->path('admin_path', 'yatpmin');
        Route::prefix($adminPath)->name('admin.')->group(function () {
            Route::middleware('guest:admin')->group(function () {
                Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
                Route::post('/login', [AdminAuthController::class, 'login'])->middleware('throttle:5,1')->name('login.attempt');
            });

            Route::middleware('auth:admin')->group(function () {
                Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
                Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

                // GOD CRUD
                Route::resource('admins', AdminController::class)->except(['show']);
                Route::resource('roles', RoleController::class)->except(['show']);
                Route::get('permissions', [PermissionController::class, 'index'])->name('permissions.index');
                Route::resource('owners', OwnerController::class)->except(['show']);
                Route::resource('plans', PlanController::class)->except(['show']);
                Route::resource('subscriptions', SubscriptionController::class)->except(['show']);
                Route::resource('tenants', TenantController::class)->except(['show']);

                Route::post('tenants/{tenant}/impersonate', [TenantController::class, 'impersonate'])->name('tenants.impersonate');

                // Subscribe (existing or new owner)
                Route::get('subscribe', [AdminSubscribeController::class, 'create'])->name('subscribe.create');
                Route::post('subscribe', [AdminSubscribeController::class, 'store'])->name('subscribe.store');

                Route::middleware('can:manage-settings')->group(function () {
                    Route::get('/settings', [SettingsController::class, 'edit'])->name('settings.edit');
                    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
                });

                // Cache management (group / sub-module / all)
                Route::prefix('cache')->name('cache.')->middleware('can:manage-settings')->group(function () {
                    Route::get('/', [CacheController::class, 'index'])->name('index');
                    Route::post('clear/{group}/{subModule?}', [CacheController::class, 'clearSubModule'])->name('clear.sub');
                    Route::post('clear-group/{group}', [CacheController::class, 'clearGroup'])->name('clear.group');
                    Route::post('clear-all', [CacheController::class, 'clearAll'])->name('clear.all');
                    Route::post('validate', [CacheController::class, 'validateStructure'])->name('validate');
                });
            });
        });
    });
}
