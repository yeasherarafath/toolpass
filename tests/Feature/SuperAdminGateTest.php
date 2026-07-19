<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Owner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TenantTestCase;

class SuperAdminGateTest extends TenantTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'PlatformSeeder', '--force' => true]);
    }

    public function test_first_admin_has_all_gates_open(): void
    {
        // The "first admin" is the one with the lowest id (earliest created).
        $first = Admin::query()->orderBy('id')->firstOrFail();

        // Arbitrary ability that was never granted — open for the first admin.
        $this->assertTrue($first->can('some-random-ability'));
    }

    public function test_other_admins_are_not_auto_granted(): void
    {
        // Any admin that is NOT the lowest-id admin falls through to normal
        // Spatie resolution (no bypass for an ungranted ability).
        $other = Admin::create([
            'name' => 'Other Admin',
            'email' => 'other'.uniqid().'@toolpass.test',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);

        $this->assertFalse($other->can('some-random-ability'));

        // The lowest-id admin still gets the bypass.
        $first = Admin::query()->orderBy('id')->firstOrFail();
        $this->assertTrue($first->can('some-random-ability'));
    }

    public function test_owner_guard_is_unaffected(): void
    {
        $owner = Owner::create([
            'name' => 'Owner',
            'email' => 'owner'.uniqid().'@toolpass.test',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);

        // Owners must NOT get the super-admin bypass.
        $this->assertFalse($owner->can('some-random-ability'));
    }
}
