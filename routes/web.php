<?php

use App\Http\Controllers\Platform\AuthController as PlatformAuthController;
use App\Http\Controllers\Platform\DashboardController as PlatformDashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Central Routes (platform / super admin + owner login)
|--------------------------------------------------------------------------
| These routes run on the central domain(s) only. Tenant business routes
| live in routes/tenant.php and are resolved by subdomain.
*/

foreach (config('tenancy.central_domains') as $domain) {
    Route::domain($domain)->group(function () {

        Route::get('/', function () {
            return redirect()->route('platform.login');
        })->name('platform.home');

        Route::middleware('guest:owner')->group(function () {
            Route::get('/login', [PlatformAuthController::class, 'showLogin'])->name('platform.login');
            Route::post('/login', [PlatformAuthController::class, 'login'])->middleware('throttle:5,1')->name('platform.login.attempt');
        });

        Route::middleware('auth:owner')->group(function () {
            Route::post('/logout', [PlatformAuthController::class, 'logout'])->name('platform.logout');
            Route::get('/dashboard', [PlatformDashboardController::class, 'index'])->name('platform.dashboard');
        });
    });
}
