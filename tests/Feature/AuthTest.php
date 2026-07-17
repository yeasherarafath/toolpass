<?php

namespace Tests\Feature;

use App\Events\User\UserCreated;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TenantTestCase;

class AuthTest extends TenantTestCase
{
    use RefreshDatabase;

    public function test_admin_can_login(): void
    {
        $admin = User::factory()->admin()->create(['password' => bcrypt('secret123')]);

        $response = $this->post('/login', [
            'email' => $admin->email,
            'password' => 'secret123',
        ]);

        $response->assertRedirect(route('business.dashboard'));
        $this->assertAuthenticatedAs($admin);
    }

    public function test_customer_can_register(): void
    {
        Event::fake([UserCreated::class]);

        $response = $this->post('/register', [
            'name' => 'New Cust',
            'email' => 'newcust@example.com',
            'phone' => '01711111111',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertRedirect(route('customer.dashboard'));
        $this->assertDatabaseHas('users', [
            'email' => 'newcust@example.com',
            'role' => 'customer',
            'status' => 'active',
        ]);
        Event::assertDispatched(UserCreated::class);
    }

    public function test_inactive_user_cannot_login(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('secret123'),
            'status' => 'suspended',
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'secret123',
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_guest_is_redirected_from_admin(): void
    {
        $this->get('/business')->assertRedirect('/login');
    }

    public function test_role_gate_blocks_wrong_role(): void
    {
        $staff = User::factory()->staff()->create();

        $this->actingAs($staff)->get('/customer')->assertForbidden();
    }

    public function test_logout_clears_session(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post('/logout')
            ->assertRedirect('/');

        $this->assertGuest();
    }
}
