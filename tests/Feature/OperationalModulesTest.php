<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Package;
use App\Models\PackageTool;
use App\Models\Tool;
use App\Models\ToolAccount;
use App\Models\UserToolAccess;
use App\Models\UserToolDevice;
use App\Models\OtpRequest;
use App\Models\DeviceResetRequest;
use App\Models\SupportTicket;
use App\Models\AdminTask;
use App\Models\User;
use App\Actions\Otp\ProvideOtpAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TenantTestCase;

class OperationalModulesTest extends TenantTestCase
{
    use RefreshDatabase;

    protected function customer(): User
    {
        return User::factory()->create(['role' => 'customer']);
    }

    protected function admin(): User
    {
        return User::factory()->admin()->create();
    }

    protected function readyAccess(User $customer, Tool $tool, ?ToolAccount $account = null): UserToolAccess
    {
        $package = Package::factory()->create(['status' => 'active', 'delivery_type' => 'manual']);
        PackageTool::factory()->create(['package_id' => $package->id, 'tool_id' => $tool->id]);
        $order = Order::factory()->create([
            'user_id' => $customer->id,
            'package_id' => $package->id,
            'payment_status' => 'paid',
            'required_info_status' => 'approved',
            'order_status' => 'ready',
        ]);
        $access = UserToolAccess::factory()->create([
            'user_id' => $customer->id,
            'order_id' => $order->id,
            'tool_id' => $tool->id,
            'tool_account_id' => $account?->id,
            'status' => 'active',
            'delivery_status' => 'delivered',
        ]);

        return $access;
    }

    public function test_customer_otp_request_creates_request_and_task(): void
    {
        Queue::fake();
        $customer = $this->customer();
        $tool = Tool::factory()->create();
        $access = $this->readyAccess($customer, $tool);

        $this->actingAs($customer)
            ->post(route('customer.otp.request'), ['user_tool_access_id' => $access->id])
            ->assertRedirect();

        $this->assertDatabaseHas('otp_requests', [
            'user_id' => $customer->id,
            'user_tool_access_id' => $access->id,
            'status' => 'pending',
        ]);
        $this->assertDatabaseHas('admin_tasks', [
            'type' => 'provide_otp',
            'otp_request_id' => OtpRequest::where('user_id', $customer->id)->latest()->first()->id,
        ]);
    }

    public function test_admin_provides_otp_and_customer_can_view_code(): void
    {
        Queue::fake();
        $customer = $this->customer();
        $admin = $this->admin();
        $tool = Tool::factory()->create();
        $access = $this->readyAccess($customer, $tool);

        $request = OtpRequest::factory()->create([
            'user_id' => $customer->id,
            'user_tool_access_id' => $access->id,
            'tool_id' => $tool->id,
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->post(route('business.otp.provide', $request))
            ->assertRedirect();

        $request->refresh();
        $this->assertEquals('provided', $request->status);
        $this->assertNotNull($request->otp_code_encrypted);

        $code = ProvideOtpAction::decrypt($request);
        $this->assertNotNull($code);
        $this->assertTrue(ProvideOtpAction::isViewable($request));

        $this->actingAs($customer)
            ->get(route('customer.otp.show', $request))
            ->assertOk()
            ->assertSee($code);
    }

    public function test_device_reset_flow_decrements_used_devices(): void
    {
        Queue::fake();
        $customer = $this->customer();
        $admin = $this->admin();
        $tool = Tool::factory()->create();
        $account = ToolAccount::factory()->create([
            'tool_id' => $tool->id,
            'max_users' => 3,
            'used_slots' => 1,
            'max_devices' => 2,
            'used_devices' => 1,
            'allow_device_reset' => true,
            'device_reset_interval_days' => 7,
            'status' => 'active',
        ]);
        $access = $this->readyAccess($customer, $tool, $account);
        $device = UserToolDevice::factory()->active()->create([
            'user_id' => $customer->id,
            'user_tool_access_id' => $access->id,
            'tool_id' => $tool->id,
            'tool_account_id' => $account->id,
        ]);

        $this->actingAs($customer)
            ->post(route('customer.devices.reset'), ['user_tool_access_id' => $access->id])
            ->assertRedirect();

        $reset = DeviceResetRequest::where('user_tool_access_id', $access->id)->latest()->first();
        $this->assertNotNull($reset);
        $this->assertDatabaseHas('admin_tasks', [
            'type' => 'device_reset',
            'device_reset_request_id' => $reset->id,
        ]);

        $this->actingAs($admin)
            ->post(route('business.device-resets.complete', $reset))
            ->assertRedirect();

        $device->refresh();
        $account->refresh();
        $this->assertEquals('removed', $device->status);
        $this->assertEquals(0, $account->used_devices);
    }

    public function test_customer_support_ticket_and_admin_reply(): void
    {
        Queue::fake();
        $customer = $this->customer();
        $admin = $this->admin();

        $this->actingAs($customer)
            ->post(route('customer.support.store'), [
                'subject' => 'Cannot login',
                'message' => 'Please help',
            ])
            ->assertRedirect();

        $ticket = SupportTicket::where('user_id', $customer->id)->latest()->first();
        $this->assertNotNull($ticket);
        $this->assertEquals('open', $ticket->status);
        $this->assertDatabaseHas('support_ticket_messages', [
            'ticket_id' => $ticket->id,
            'is_staff_reply' => false,
        ]);

        $this->actingAs($admin)
            ->post(route('business.support.reply', $ticket), ['message' => 'We are on it'])
            ->assertRedirect();

        $ticket->refresh();
        $this->assertEquals('pending_customer', $ticket->status);

        $this->actingAs($admin)
            ->post(route('business.support.close', $ticket))
            ->assertRedirect();

        $ticket->refresh();
        $this->assertEquals('closed', $ticket->status);
    }
}
