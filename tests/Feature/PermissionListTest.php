<?php

namespace Tests\Feature;

use App\Models\Admin;
use Database\Seeders\PermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TenantTestCase;

class PermissionListTest extends TenantTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'PlatformSeeder', '--force' => true]);
    }

    public function test_permissions_index_lists_all_admin_permissions(): void
    {
        $admin = Admin::where('email', 'superadmin@toolpass.test')->first();
        $this->actingAs($admin, 'admin');

        $response = $this->withServerVariables(['HTTP_HOST' => '127.0.0.1'])
            ->get(route('admin.permissions.index'))
            ->assertOk();

        foreach (PermissionsSeeder::permissions() as $name) {
            $response->assertSee($name);
        }
    }

    public function test_permissions_are_grouped_by_prefix(): void
    {
        $admin = Admin::where('email', 'superadmin@toolpass.test')->first();
        $this->actingAs($admin, 'admin');

        $response = $this->withServerVariables(['HTTP_HOST' => '127.0.0.1'])
            ->get(route('admin.permissions.index'))
            ->assertOk();

        $groups = $response->viewData('groups');

        $this->assertArrayHasKey('manage', $groups);
        $this->assertArrayHasKey('impersonate', $groups);
        $this->assertArrayHasKey('send', $groups);
        $this->assertArrayHasKey('view', $groups);
    }
}
