<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
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
use App\Events\User\UserCreated;
use App\Events\ToolAccount\ToolAccountCreated;
use App\Events\Order\OrderCreated;
use App\Events\Payment\PaymentVerified;
use App\Events\Access\AccessDelivered;
use App\Events\Access\AccessExpired;
use App\Listeners\SendWelcomeNotification;
use App\Listeners\NotifyToolAccountAdded;
use App\Listeners\NotifyOrderPlaced;
use App\Listeners\NotifyPaymentVerified;
use App\Listeners\NotifyAccessDelivered;
use App\Listeners\NotifyAccessExpired;

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

        Event::listen(UserCreated::class, SendWelcomeNotification::class);
        Event::listen(ToolAccountCreated::class, NotifyToolAccountAdded::class);
        Event::listen(OrderCreated::class, NotifyOrderPlaced::class);
        Event::listen(PaymentVerified::class, NotifyPaymentVerified::class);
        Event::listen(AccessDelivered::class, NotifyAccessDelivered::class);
        Event::listen(AccessExpired::class, NotifyAccessExpired::class);
    }
}
