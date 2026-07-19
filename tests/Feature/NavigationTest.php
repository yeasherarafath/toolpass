<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Owner;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TenantTestCase;

class NavigationTest extends TenantTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'PlatformSeeder', '--force' => true]);
    }

    public function test_god_panel_renders_sidebar_and_topbar(): void
    {
        $admin = Admin::where('email', 'superadmin@toolpass.test')->first();
        $this->actingAs($admin, 'admin');

        $response = $this->withServerVariables(['HTTP_HOST' => '127.0.0.1'])
            ->get(route('admin.dashboard'))
            ->assertOk();

        // Sidebar nav items.
        foreach (['Admins', 'Roles & Permissions', 'Owners', 'Plans', 'Subscriptions', 'Tenants', 'Cache Management'] as $label) {
            $response->assertSee($label);
        }

        // Topbar user menu + logout.
        $response->assertSee('Logout');
        $response->assertSee($admin->name);
    }

    public function test_owner_panel_renders_sidebar_and_topbar(): void
    {
        $owner = Owner::create([
            'name' => 'Owner',
            'email' => 'owner'.uniqid().'@toolpass.test',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);

        $this->actingAs($owner, 'owner');

        $response = $this->withServerVariables(['HTTP_HOST' => '127.0.0.1'])
            ->get(route('owner.dashboard'))
            ->assertOk();

        $response->assertSee('Business Settings');
        $response->assertSee('Logout');
    }

    public function test_customer_panel_renders_sidebar_and_topbar(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $this->actingAs($customer);

        $response = $this->withServerVariables(['HTTP_HOST' => 'tenant.localhost'])
            ->get(route('customer.dashboard'))
            ->assertOk();

        foreach (['My Dashboard', 'My Orders', 'Support'] as $label) {
            $response->assertSee($label);
        }

        $response->assertSee('Logout');
    }
}
