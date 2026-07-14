<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    private function admin()
    {
        return User::factory()->admin()->create();
    }

    public function test_admin_can_view_users(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.users.index'))
            ->assertOk();
    }

    public function test_admin_can_create_user(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.users.store'), [
                'name' => 'Jane Staff',
                'email' => 'jane@example.com',
                'phone' => '01722222222',
                'role' => 'staff',
                'status' => 'active',
                'password' => 'secret123',
            ])
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', ['email' => 'jane@example.com', 'role' => 'staff']);
    }

    public function test_admin_can_toggle_status(): void
    {
        $admin = $this->admin();
        $user = User::factory()->create(['status' => 'active']);

        $this->actingAs($admin)
            ->post(route('admin.users.toggle-status', $user))
            ->assertRedirect();

        $this->assertDatabaseHas('users', ['id' => $user->id, 'status' => 'suspended']);
    }

    public function test_admin_cannot_toggle_own_status(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->post(route('admin.users.toggle-status', $admin))
            ->assertSessionHasErrors('status');

        $this->assertDatabaseHas('users', ['id' => $admin->id, 'status' => 'active']);
    }
}
