<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Services\Settings;
use App\Services\TenantSettings;
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
use App\Events\Otp\OtpProvided;
use App\Listeners\SendWelcomeNotification;
use App\Listeners\NotifyToolAccountAdded;
use App\Listeners\NotifyOrderPlaced;
use App\Listeners\NotifyPaymentVerified;
use App\Listeners\NotifyAccessDelivered;
use App\Listeners\NotifyAccessExpired;
use App\Listeners\NotifyOtpProvided;
use App\Listeners\WriteActivityLog;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        if ($this->app->environment('testing')) {
            $this->loadMigrationsFrom(database_path('migrations/tenant'));
        }

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
        Event::listen(OtpProvided::class, NotifyOtpProvided::class);
        Event::listen(OtpProvided::class, WriteActivityLog::class);

        $this->shareBranding();
        $this->shareHelper();
    }

    /**
     * Make the App\Helper class available in all Blade views as $helper.
     */
    protected function shareHelper(): void
    {
        View::composer('*', function ($view) {
            $view->with('helper', \App\Helper::class);
        });
    }

    /**
     * Share branding to all views. Precedence: tenant business settings ->
     * central platform settings -> config('app.name'). Wrapped defensively so
     * it is a no-op during migrations / before tables exist.
     */
    protected function shareBranding(): void
    {
        View::composer('*', function ($view) {
            $branding = [
                'site_name' => config('app.name', 'ToolPass'),
                'site_description' => null,
                'site_keywords' => null,
                'logo_path' => null,
                'favicon_path' => null,
                'footer_text' => config('app.name', 'ToolPass'),
                'logo_disk' => 'public',
            ];

            try {
                $central = app(Settings::class);
                $branding['site_name'] = $central->get('site_name') ?: $branding['site_name'];
                $branding['site_description'] = $central->get('site_description');
                $branding['site_keywords'] = $central->get('site_keywords');
                $branding['logo_path'] = $central->get('logo_path');
                $branding['favicon_path'] = $central->get('favicon_path');
                $branding['footer_text'] = $central->get('footer_text') ?: $branding['footer_text'];

                if (function_exists('tenant') && tenant()) {
                    $tenantSettings = app(TenantSettings::class);
                    $branding['site_name'] = $tenantSettings->get('business_name') ?: $branding['site_name'];
                    $branding['site_description'] = $tenantSettings->get('business_description') ?: $branding['site_description'];
                    $branding['logo_path'] = $tenantSettings->get('logo_path') ?: $branding['logo_path'];
                    $branding['favicon_path'] = $tenantSettings->get('favicon_path') ?: $branding['favicon_path'];
                    $branding['footer_text'] = $tenantSettings->get('business_name') ?: $branding['footer_text'];
                }
            } catch (\Throwable $e) {
                // tables not migrated yet (e.g. during install) - use fallbacks
            }

            $view->with('branding', $branding);
        });
    }
}
