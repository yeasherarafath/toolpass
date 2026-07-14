<?php

namespace Tests\Feature;

use App\Actions\Otp\RequestOtpAction;
use App\Models\ToolAccount;
use App\Models\User;
use App\Models\UserToolAccess;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Crypt;
use Tests\TestCase;

class SecurityAndDeployTest extends TestCase
{
    use RefreshDatabase;
    public function test_access_expire_command_frees_account_slot(): void
    {
        $account = ToolAccount::factory()->create([
            'max_users' => 1,
            'used_slots' => 0,
            'status' => 'active',
        ]);

        $access = UserToolAccess::factory()->create([
            'tool_account_id' => $account->id,
            'status' => 'active',
            'expires_at' => now()->subDay(),
        ]);

        $account->refresh();
        $this->assertSame(1, (int) $account->used_slots);

        Artisan::call('access:expire');

        $access->refresh();
        $account->refresh();

        $this->assertSame('expired', $access->status);
        $this->assertSame(0, (int) $account->used_slots);
    }

    public function test_credentials_only_visible_to_staff_via_policy(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $admin = User::factory()->create(['role' => 'admin']);
        $account = ToolAccount::factory()->create();

        $this->assertFalse($customer->can('viewCredentials', $account));
        $this->assertTrue($admin->can('viewCredentials', $account));
    }

    public function test_otp_rate_limit_blocks_after_threshold(): void
    {
        $user = User::factory()->create();
        $account = ToolAccount::factory()->create();
        $access = UserToolAccess::factory()->create([
            'user_id' => $user->id,
            'tool_account_id' => $account->id,
            'status' => 'active',
        ]);

        $action = app(RequestOtpAction::class);

        for ($i = 1; $i <= 3; $i++) {
            $action->handle($user, [
                'tool_id' => $account->tool_id,
                'tool_account_id' => $account->id,
                'user_tool_access_id' => $access->id,
            ]);
        }

        $this->expectException(\RuntimeException::class);
        $action->handle($user, [
            'tool_id' => $account->tool_id,
            'tool_account_id' => $account->id,
            'user_tool_access_id' => $access->id,
        ]);
    }

    public function test_tool_account_masks_login_password(): void
    {
        $account = ToolAccount::factory()->create([
            'login_password_encrypted' => Crypt::encrypt('secret123'),
        ]);

        $masked = $account->maskedLoginPassword();

        $this->assertNotSame('secret123', $masked);
        $this->assertStringContainsString('*', $masked);
        $this->assertStringNotContainsString('secret', $masked);
    }
}
