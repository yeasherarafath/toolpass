<?php

namespace Tests\Feature;

use App\Enum\CacheKeyEnum;
use App\Models\Package;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TenantTestCase;

class StorefrontCacheTest extends TenantTestCase
{
    use RefreshDatabase;

    public function test_storefront_packages_and_banners_are_cached_per_tenant(): void
    {
        $tenantId = $this->tenant->getTenantKey();

        $this->get(route('store.index'))->assertOk();

        $this->assertTrue(
            \Illuminate\Support\Facades\Cache::has(CacheKeyEnum::STOREFRONT_PACKAGES_PREFIX->value.$tenantId)
        );
        $this->assertTrue(
            \Illuminate\Support\Facades\Cache::has(CacheKeyEnum::STOREFRONT_BANNERS_PREFIX->value.$tenantId)
        );
    }

    public function test_storefront_cache_is_invalidated_by_pattern_service(): void
    {
        $tenantId = $this->tenant->getTenantKey();

        // The storefront list (pattern key) lives in the database cache store
        // in production; the admin controllers flush it via this service.
        $key = CacheKeyEnum::STOREFRONT_PACKAGES_PREFIX->value.$tenantId;

        \Illuminate\Support\Facades\Cache::store('database')->put($key, ['cached'], 3600);

        $this->assertTrue(
            \Illuminate\Support\Facades\Cache::store('database')->has($key)
        );

        app(\App\Services\CachePatternService::class)
            ->clearByPattern('storefront:packages:*');

        $this->assertFalse(
            \Illuminate\Support\Facades\Cache::store('database')->has($key)
        );
    }
}
