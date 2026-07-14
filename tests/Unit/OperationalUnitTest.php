<?php

namespace Tests\Unit;

use App\Actions\AdminTask\CreateAdminTaskAction;
use App\Actions\AdminTask\CompleteAdminTaskAction;
use App\Actions\Device\RequestDeviceResetAction;
use App\Actions\Otp\ProvideOtpAction;
use App\Events\Otp\OtpProvided;
use App\Listeners\NotifyOtpProvided;
use App\Listeners\WriteActivityLog;
use App\Models\AdminTask;
use App\Models\Tool;
use App\Models\ToolAccount;
use App\Models\UserToolAccess;
use App\Models\OtpRequest;
use App\Models\Notification;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class AdminTaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_task_dedupe_returns_existing_open_task(): void
    {
        $task = app(CreateAdminTaskAction::class)->handle([
            'type' => 'provide_otp',
            'title' => 'Provide OTP',
            'otp_request_id' => 5,
        ]);

        $again = app(CreateAdminTaskAction::class)->handle([
            'type' => 'provide_otp',
            'title' => 'Provide OTP again',
            'otp_request_id' => 5,
        ]);

        $this->assertEquals($task->id, $again->id);
        $this->assertDatabaseCount('admin_tasks', 1);
    }

    public function test_completing_invite_task_delivers_access(): void
    {
        Queue::fake();

        $access = UserToolAccess::factory()->create([
            'status' => 'pending',
            'delivery_status' => 'pending',
        ]);

        $task = AdminTask::factory()->create([
            'type' => 'invite_user',
            'user_tool_access_id' => $access->id,
            'status' => 'open',
        ]);

        app(CompleteAdminTaskAction::class)->handle($task, 1);

        $task->refresh();
        $access->refresh();
        $this->assertEquals('completed', $task->status);
        $this->assertEquals('delivered', $access->delivery_status);
    }
}

class DevicePolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_blocked_when_not_allowed(): void
    {
        $tool = Tool::factory()->create();
        $account = ToolAccount::factory()->create([
            'tool_id' => $tool->id,
            'allow_device_reset' => false,
            'max_devices' => 2,
            'used_devices' => 1,
        ]);
        $access = UserToolAccess::factory()->create([
            'tool_id' => $tool->id,
            'tool_account_id' => $account->id,
        ]);

        $this->expectException(\RuntimeException::class);
        app(RequestDeviceResetAction::class)->handle($access, User::factory()->create());
    }

    public function test_reset_allowed_when_enabled(): void
    {
        Queue::fake();

        $tool = Tool::factory()->create();
        $account = ToolAccount::factory()->create([
            'tool_id' => $tool->id,
            'allow_device_reset' => true,
            'device_reset_interval_days' => 7,
            'max_devices' => 2,
            'used_devices' => 1,
        ]);
        $access = UserToolAccess::factory()->create([
            'tool_id' => $tool->id,
            'tool_account_id' => $account->id,
        ]);

        $request = app(RequestDeviceResetAction::class)->handle($access, User::factory()->create());
        $this->assertEquals('pending', $request->status);
    }
}

class OtpListenerTest extends TestCase
{
    use RefreshDatabase;

    public function test_otp_provided_listener_writes_notification_and_log(): void
    {
        $user = User::factory()->create();
        $tool = Tool::factory()->create();
        $request = OtpRequest::factory()->create([
            'user_id' => $user->id,
            'tool_id' => $tool->id,
            'status' => 'provided',
        ]);

        (new NotifyOtpProvided())->handle(new OtpProvided($request));
        (new WriteActivityLog())->handle(new OtpProvided($request));

        $this->assertDatabaseHas('notifications', [
            'user_id' => $user->id,
            'type' => 'otp',
        ]);
        $this->assertDatabaseHas('activity_logs', [
            'description' => 'OTP provided for ' . $tool->name,
        ]);
    }
}
