<?php

namespace Tests\Feature;

use App\Enum\CacheKeyEnum;
use App\Models\Admin;
use App\Models\Owner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TenantTestCase;

class DashboardCacheTest extends TenantTestCase
{
    use RefreshDatabase;

    protected function central(string $uri = ''): string
    {
        return 'http://127.0.0.1' . $uri;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'PlatformSeeder', '--force' => true]);
    }

    public function test_admin_dashboard_is_cached(): void
    {
        $admin = Admin::where('email', 'superadmin@toolpass.test')->first();
        $this->actingAs($admin, 'admin');

        $this->withServerVariables(['HTTP_HOST' => '127.0.0.1'])
            ->get(route('admin.dashboard'))
            ->assertOk();

        $this->assertTrue(Cache::has(CacheKeyEnum::ADMIN_DASHBOARD_WIDGETS->value));
    }

    public function test_owner_dashboard_is_cached_per_owner(): void
    {
        $owner = Owner::create([
            'name' => 'Biz Owner',
            'email' => 'biz@toolpass.test',
            'password' => bcrypt('secret123'),
            'business_name' => 'Biz Co',
            'status' => 'active',
        ]);
        $this->actingAs($owner, 'owner');

        $this->withServerVariables(['HTTP_HOST' => '127.0.0.1'])
            ->get(route('owner.dashboard'))
            ->assertOk();

        $this->assertTrue(
            Cache::has(CacheKeyEnum::OWNER_DASHBOARD_WIDGETS->value . ':' . $owner->getKey())
        );
    }

    public function test_dashboard_cache_keys_registered(): void
    {
        $this->assertTrue(CacheKeyEnum::validateStructure()['passed']);
    }
}
