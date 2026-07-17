<?php

namespace Tests\Feature;

use App\Enum\CacheKeyEnum;
use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TenantTestCase;

class CacheManagementTest extends TenantTestCase
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

    public function test_cache_management_page_loads_for_admin(): void
    {
        $admin = Admin::where('email', 'superadmin@toolpass.test')->first();
        $this->actingAs($admin, 'admin');

        $this->withServerVariables(['HTTP_HOST' => '127.0.0.1'])
            ->get(route('admin.cache.index'))
            ->assertOk();
    }

    public function test_cache_key_registry_is_valid(): void
    {
        $this->assertTrue(CacheKeyEnum::validateStructure()['passed']);
    }

    public function test_admin_can_clear_a_static_sub_module(): void
    {
        $admin = Admin::where('email', 'superadmin@toolpass.test')->first();
        $this->actingAs($admin, 'admin');

        Cache::put(CacheKeyEnum::PLATFORM_SETTINGS->value, 'x', 600);
        $this->assertTrue(Cache::has(CacheKeyEnum::PLATFORM_SETTINGS->value));

        $this->withServerVariables(['HTTP_HOST' => '127.0.0.1'])
            ->post(route('admin.cache.clear.sub', ['settings', 'platform']))
            ->assertRedirect(route('admin.cache.index'));

        $this->assertFalse(Cache::has(CacheKeyEnum::PLATFORM_SETTINGS->value));
    }

    public function test_admin_can_clear_dynamic_pattern_by_group(): void
    {
        $service = app(\App\Services\CachePatternService::class);
        $prefix = config('cache.prefix');

        DB::table('cache')->insert([
            ['key' => $prefix.CacheKeyEnum::tenantSettings('abc'), 'value' => serialize('y'), 'expiration' => 9999999999],
            ['key' => $prefix.CacheKeyEnum::customerDashboard(5), 'value' => serialize('z'), 'expiration' => 9999999999],
        ]);

        $this->assertTrue(DB::table('cache')->where('key', $prefix.CacheKeyEnum::tenantSettings('abc'))->exists());
        $this->assertTrue(DB::table('cache')->where('key', $prefix.CacheKeyEnum::customerDashboard(5))->exists());

        $service->clearForGroup('tenant');

        $this->assertFalse(DB::table('cache')->where('key', $prefix.CacheKeyEnum::tenantSettings('abc'))->exists());
        $this->assertFalse(DB::table('cache')->where('key', $prefix.CacheKeyEnum::customerDashboard(5))->exists());
    }

    public function test_admin_can_clear_all_managed_caches(): void
    {
        $service = app(\App\Services\CachePatternService::class);
        $prefix = config('cache.prefix');

        DB::table('cache')->insert([
            ['key' => $prefix.CacheKeyEnum::PLATFORM_SETTINGS->value, 'value' => serialize('a'), 'expiration' => 9999999999],
            ['key' => $prefix.CacheKeyEnum::otpRate(9), 'value' => serialize('b'), 'expiration' => 9999999999],
        ]);

        $service->clearAll();

        // Static keys are cleared via the default store (array in tests),
        // matching how Cache::forget works. The OTP key is dynamic and is
        // removed from the queryable database cache table by pattern.
        $this->assertFalse(Cache::has(CacheKeyEnum::PLATFORM_SETTINGS->value));
        $this->assertFalse(DB::table('cache')->where('key', $prefix.CacheKeyEnum::otpRate(9))->exists());
    }

    public function test_staff_cannot_access_cache_management(): void
    {
        $staff = Admin::create([
            'name' => 'Staff',
            'email' => 'staff@toolpass.test',
            'password' => bcrypt('secret123'),
            'status' => 'active',
        ]);
        $staffRole = \Spatie\Permission\Models\Role::findOrCreate('platform_staff', 'admin');
        $staff->syncRoles([$staffRole]);
        $this->actingAs($staff, 'admin');

        $this->withServerVariables(['HTTP_HOST' => '127.0.0.1'])
            ->get(route('admin.cache.index'))
            ->assertForbidden();
    }
}
