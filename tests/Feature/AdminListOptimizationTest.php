<?php

namespace Tests\Feature;

use App\Enum\CacheKeyEnum;
use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TenantTestCase;

class AdminListOptimizationTest extends TenantTestCase
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

    public function test_tenant_list_eager_loads_domains(): void
    {
        $admin = Admin::where('email', 'superadmin@toolpass.test')->first();
        $this->actingAs($admin, 'admin');

        $response = $this->withServerVariables(['HTTP_HOST' => '127.0.0.1'])
            ->get(route('admin.tenants.index'))
            ->assertOk();

        $tenants = $response->viewData('tenants');
        $this->assertTrue($tenants->first()->relationLoaded('domains'));
    }

    public function test_role_edit_preloads_permissions(): void
    {
        $admin = Admin::where('email', 'superadmin@toolpass.test')->first();
        $this->actingAs($admin, 'admin');

        $role = Role::where('guard_name', 'admin')->firstOrFail();

        $response = $this->withServerVariables(['HTTP_HOST' => '127.0.0.1'])
            ->get(route('admin.roles.edit', $role))
            ->assertOk();

        $this->assertTrue($response->viewData('role')->relationLoaded('permissions'));
    }

    public function test_storefront_cache_keys_registered(): void
    {
        $this->assertTrue(CacheKeyEnum::validateStructure()['passed']);
    }

    public function test_tenant_list_is_cached_and_invalidated(): void
    {
        $admin = Admin::where('email', 'superadmin@toolpass.test')->first();
        $this->actingAs($admin, 'admin');

        $this->withServerVariables(['HTTP_HOST' => '127.0.0.1'])
            ->get(route('admin.tenants.index'))
            ->assertOk();

        $this->assertTrue(
            \Illuminate\Support\Facades\Cache::has(CacheKeyEnum::ADMIN_TENANTS_LIST->value.':1')
        );

        // Creating a tenant via the controller must flush the cached list.
        $this->withServerVariables(['HTTP_HOST' => '127.0.0.1'])
            ->post(route('admin.tenants.store'), [
                'id' => 'newco'.uniqid(),
                'business_name' => 'New Co',
                'status' => 'active',
            ])
            ->assertRedirect();

        $this->assertFalse(
            \Illuminate\Support\Facades\Cache::has(CacheKeyEnum::ADMIN_TENANTS_LIST->value.':1')
        );
    }
}
