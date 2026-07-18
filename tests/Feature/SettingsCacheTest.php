<?php

namespace Tests\Feature;

use App\Enum\CacheKeyEnum;
use App\Models\PlatformSetting;
use App\Models\Setting;
use App\Services\Settings;
use App\Services\TenantSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TenantTestCase;

class SettingsCacheTest extends TenantTestCase
{
    use RefreshDatabase;

    public function test_platform_settings_are_cached_and_invalidated(): void
    {
        $service = app(Settings::class);

        $service->set('support_email', 'a@b.com', false, 'general');
        $this->assertSame('a@b.com', $service->get('support_email'));

        // Direct model-instance mutation must invalidate the cache (observer).
        $model = PlatformSetting::where('key', 'support_email')->first();
        $model->value = 'c@d.com';
        $model->save();

        $fresh = app(Settings::class);
        $this->assertSame('c@d.com', $fresh->get('support_email'));
    }

    public function test_tenant_settings_are_cached_and_invalidated(): void
    {
        $service = app(TenantSettings::class);

        $service->set('business_name', 'Acme Co', false, 'business');
        $this->assertSame('Acme Co', $service->get('business_name'));

        $key = CacheKeyEnum::tenantSettings((string) $this->tenant->getTenantKey());
        $this->assertTrue(\Illuminate\Support\Facades\Cache::has($key));

        // Direct model-instance mutation must invalidate the tenant settings cache.
        $model = Setting::where('key', 'business_name')->first();
        $model->value = 'Acme Updated';
        $model->save();

        $fresh = app(TenantSettings::class);
        $this->assertSame('Acme Updated', $fresh->get('business_name'));
    }
}
