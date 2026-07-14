<?php

namespace Tests\Unit;

use App\Events\ToolAccount\ToolAccountCreated;
use App\Events\User\UserCreated;
use App\Listeners\NotifyToolAccountAdded;
use App\Listeners\SendWelcomeNotification;
use App\Models\Notification;
use App\Models\Tool;
use App\Models\ToolAccount;
use App\Models\ToolCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListenerTest extends TestCase
{
    use RefreshDatabase;

    public function test_welcome_notification_created_for_customer(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        (new SendWelcomeNotification())->handle(new UserCreated($customer));

        $this->assertDatabaseHas('notifications', [
            'user_id' => $customer->id,
            'type' => 'announcement',
            'subject' => 'Welcome to ToolPass',
            'status' => 'pending',
        ]);
    }

    public function test_welcome_listener_ignores_non_customer(): void
    {
        $admin = User::factory()->admin()->create();

        (new SendWelcomeNotification())->handle(new UserCreated($admin));

        $this->assertDatabaseMissing('notifications', [
            'subject' => 'Welcome to ToolPass',
        ]);
    }

    public function test_tool_account_added_notification(): void
    {
        $creator = User::factory()->admin()->create();
        $category = ToolCategory::factory()->create();
        $tool = Tool::factory()->create(['category_id' => $category->id]);
        $account = ToolAccount::factory()->create([
            'tool_id' => $tool->id,
            'created_by' => $creator->id,
        ]);

        (new NotifyToolAccountAdded())->handle(new ToolAccountCreated($account));

        $this->assertDatabaseHas('notifications', [
            'user_id' => $creator->id,
            'type' => 'announcement',
        ]);
    }

    public function test_tool_account_listener_ignores_unowned(): void
    {
        $category = ToolCategory::factory()->create();
        $tool = Tool::factory()->create(['category_id' => $category->id]);
        $account = ToolAccount::factory()->create(['tool_id' => $tool->id, 'created_by' => null]);

        (new NotifyToolAccountAdded())->handle(new ToolAccountCreated($account));

        $this->assertDatabaseCount('notifications', 0);
    }
}
