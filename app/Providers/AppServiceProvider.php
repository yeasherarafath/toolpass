<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\User;
use App\Models\ToolAccount;
use App\Models\Order;
use App\Models\UserToolAccess;
use App\Models\OtpRequest;
use App\Models\DeviceResetRequest;
use App\Models\Payment;
use App\Observers\UserObserver;
use App\Observers\ToolAccountObserver;
use App\Observers\OrderObserver;
use App\Observers\UserToolAccessObserver;
use App\Observers\OtpRequestObserver;
use App\Observers\DeviceResetRequestObserver;
use App\Observers\PaymentObserver;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        User::observe(UserObserver::class);
        ToolAccount::observe(ToolAccountObserver::class);
        Order::observe(OrderObserver::class);
        UserToolAccess::observe(UserToolAccessObserver::class);
        OtpRequest::observe(OtpRequestObserver::class);
        DeviceResetRequest::observe(DeviceResetRequestObserver::class);
        Payment::observe(PaymentObserver::class);
    }
}
