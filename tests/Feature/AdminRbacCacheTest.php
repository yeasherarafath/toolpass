<?php

namespace Tests\Feature;

use App\Enum\CacheKeyEnum;
use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TenantTestCase;

class AdminRbacCacheTest extends TenantTestCase
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

    public function test_roles_list_is_cached(): void
    {
        $admin = Admin::where('email', 'superadmin@toolpass.test')->first();
        $this->actingAs($admin, 'admin');

        $this->withServerVariables(['HTTP_HOST' => '127.0.0.1'])
            ->get(route('admin.roles.index'))
            ->assertOk();

        $this->assertTrue(Cache::has(CacheKeyEnum::ADMIN_ROLES_LIST->value));
    }

    public function test_permissions_list_is_cached_on_role_create(): void
    {
        $admin = Admin::where('email', 'superadmin@toolpass.test')->first();
        $this->actingAs($admin, 'admin');

        $this->withServerVariables(['HTTP_HOST' => '127.0.0.1'])
            ->get(route('admin.roles.create'))
            ->assertOk();

        $this->assertTrue(Cache::has(CacheKeyEnum::ADMIN_PERMISSIONS_LIST->value));
    }

    public function test_creating_role_flushes_caches(): void
    {
        $admin = Admin::where('email', 'superadmin@toolpass.test')->first();
        $this->actingAs($admin, 'admin');

        $this->withServerVariables(['HTTP_HOST' => '127.0.0.1'])
            ->get(route('admin.roles.index'));
        $this->assertTrue(Cache::has(CacheKeyEnum::ADMIN_ROLES_LIST->value));

        $perm = Permission::findOrCreate('view-x', 'admin');

        $this->withServerVariables(['HTTP_HOST' => '127.0.0.1'])
            ->post(route('admin.roles.store'), [
                'name' => 'Temp Role',
                'permissions' => [$perm->id],
            ])
            ->assertRedirect(route('admin.roles.index'));

        $this->assertFalse(Cache::has(CacheKeyEnum::ADMIN_ROLES_LIST->value));
        $this->assertFalse(Cache::has(CacheKeyEnum::ADMIN_PERMISSIONS_LIST->value));
    }

    public function test_rbac_cache_keys_registered(): void
    {
        $this->assertTrue(CacheKeyEnum::validateStructure()['passed']);
    }
}
