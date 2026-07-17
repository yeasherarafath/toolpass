<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Owner;
use App\Models\Plan;
use App\Models\Tenant;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TenantTestCase;

class PlatformAdminTest extends TenantTestCase
{
    use RefreshDatabase;

    protected function central(string $uri = ''): string
    {
        return 'http://127.0.0.1' . $uri;
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Seed admin-guard roles/permissions and a default plan.
        $this->artisan('db:seed', ['--class' => 'PlatformSeeder', '--force' => true]);
    }

    public function test_god_admin_can_login_and_see_dashboard(): void
    {
        $admin = Admin::where('email', 'superadmin@toolpass.test')->first();

        $this->withServerVariables(['HTTP_HOST' => '127.0.0.1'])
            ->post($this->central('/yatpmin/login'), [
                'email' => $admin->email,
                'password' => 'password',
            ])
            ->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($admin, 'admin');
    }

    public function test_admin_login_prefix_comes_from_settings(): void
    {
        $this->assertStringContainsString(
            '/yatpmin/login',
            route('admin.login')
        );
        $this->assertStringContainsString(
            '/business/login',
            route('owner.login')
        );
    }

    public function test_guest_cannot_access_admin_dashboard(): void
    {
        $this->withServerVariables(['HTTP_HOST' => '127.0.0.1'])
            ->get($this->central('/yatpmin/dashboard'))
            ->assertRedirect(route('admin.login'));
    }

    public function test_super_admin_can_crud_plans(): void
    {
        $admin = Admin::where('email', 'superadmin@toolpass.test')->first();
        $this->actingAs($admin, 'admin');

        $this->withServerVariables(['HTTP_HOST' => '127.0.0.1'])
            ->get(route('admin.plans.index'))
            ->assertOk();

        $this->withServerVariables(['HTTP_HOST' => '127.0.0.1'])
            ->post(route('admin.plans.store'), [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'price' => 9999,
                'currency' => 'BDT',
                'billing_cycle' => 'monthly',
                'status' => 'active',
                'sort_order' => 5,
            ])
            ->assertRedirect(route('admin.plans.index'));

        $this->assertDatabaseHas('plans', ['slug' => 'enterprise']);
    }

    public function test_platform_staff_is_forbidden_from_settings(): void
    {
        $staff = Admin::create([
            'name' => 'Staff',
            'email' => 'staff@toolpass.test',
            'password' => bcrypt('secret123'),
            'status' => 'active',
        ]);
        $staffRole = Role::findOrCreate('platform_staff', 'admin');
        $staff->syncRoles([$staffRole]);

        $this->actingAs($staff, 'admin');

        $this->withServerVariables(['HTTP_HOST' => '127.0.0.1'])
            ->get(route('admin.settings.edit'))
            ->assertForbidden();
    }

    public function test_admin_subscribe_creates_owner_tenant_and_subscription(): void
    {
        $admin = Admin::where('email', 'superadmin@toolpass.test')->first();
        $plan = Plan::where('slug', 'starter')->first();
        $this->actingAs($admin, 'admin');

        $this->withServerVariables(['HTTP_HOST' => '127.0.0.1'])
            ->post(route('admin.subscribe.store'), [
                'name' => 'New Biz',
                'email' => 'newbiz@toolpass.test',
                'password' => 'secret123',
                'business_name' => 'New Biz Co',
                'slug' => 'newbiz',
                'plan_id' => $plan->id,
                'status' => 'active',
                'amount' => $plan->price,
            ])
            ->assertRedirect(route('admin.subscriptions.edit', Subscription::latest()->first()));

        $this->assertDatabaseHas('owners', ['email' => 'newbiz@toolpass.test']);
        $this->assertDatabaseHas('tenants', ['id' => 'newbiz']);
        $this->assertDatabaseHas('subscriptions', ['plan_id' => $plan->id]);
    }

    public function test_admin_can_impersonate_tenant_user(): void
    {
        $admin = Admin::where('email', 'superadmin@toolpass.test')->first();
        $this->actingAs($admin, 'admin');

        $testDatabase = config('database.connections.' . config('database.default') . '.database');

        $tenant = Tenant::create([
            'id' => 'impco',
            'business_name' => 'Imp Co',
            'status' => 'active',
            'tenancy_db_name' => $testDatabase,
        ]);
        $tenant->domains()->create(['domain' => 'impco']);

        tenancy()->initialize($tenant);
        $user = User::create([
            'name' => 'Tenant User',
            'email' => 'tu@impco.test',
            'password' => bcrypt('secret123'),
            'role' => 'admin',
        ]);
        tenancy()->end();

        $this->withServerVariables(['HTTP_HOST' => '127.0.0.1'])
            ->post(route('admin.tenants.impersonate', $tenant), ['user_id' => $user->id])
            ->assertRedirect();

        $this->assertDatabaseHas('impersonation_tokens', ['user_id' => (string) $user->id]);
    }
}
