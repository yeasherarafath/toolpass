<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Package;
use App\Models\PackageCustomField;
use App\Models\PackageTool;
use App\Models\Payment;
use App\Models\Tool;
use App\Models\ToolAccount;
use App\Models\User;
use App\Models\UserToolAccess;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class OrderPipelineTest extends TestCase
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

    public function test_full_purchase_pipeline_creates_and_delivers_access(): void
    {
        Queue::fake();

        $customer = $this->customer();
        $admin = $this->admin();

        $tool = Tool::factory()->create();
        ToolAccount::factory()->create([
            'tool_id' => $tool->id,
            'max_users' => 5,
            'used_slots' => 0,
            'status' => 'active',
        ]);

        $package = Package::factory()->create(['status' => 'active', 'delivery_type' => 'manual']);
        PackageTool::factory()->create(['package_id' => $package->id, 'tool_id' => $tool->id]);
        $field = PackageCustomField::factory()->inviteEmail()->create([
            'package_id' => $package->id,
            'is_required' => true,
        ]);

        // 1. Customer places the order
        $this->actingAs($customer)
            ->post(route('customer.orders.store'), ['package_id' => $package->id])
            ->assertRedirectContains('/customer/orders/');

        $order = Order::where('user_id', $customer->id)->latest()->first();
        $this->assertEquals('pending', $order->required_info_status);
        $this->assertEquals('pending', $order->payment_status);
        $this->assertEquals('pending', $order->order_status);

        // 2. Customer submits required information
        $this->actingAs($customer)
            ->post(route('customer.orders.info', $order), ['fields' => [$field->id => 'buyer@example.com']])
            ->assertRedirect();

        $order->refresh();
        $this->assertEquals('submitted', $order->required_info_status);
        $this->assertDatabaseHas('order_custom_field_values', [
            'order_id' => $order->id,
            'value' => 'buyer@example.com',
        ]);

        // 3. Admin approves required information (not ready yet: payment pending)
        $this->actingAs($admin)
            ->post(route('admin.orders.review-info', $order), ['decision' => 'approve'])
            ->assertRedirect();

        $order->refresh();
        $this->assertEquals('approved', $order->required_info_status);
        $this->assertEquals('pending', $order->order_status);

        // 4. Customer submits payment
        $this->actingAs($customer)
            ->post(route('customer.orders.payments', $order), [
                'amount' => $order->payable_amount,
                'method' => 'bkash',
                'sender_number' => '01700000000',
            ])
            ->assertRedirect();

        $payment = Payment::where('order_id', $order->id)->latest()->first();
        $this->assertEquals('pending', $payment->status);

        // 5. Admin verifies payment -> order becomes ready -> accesses created
        $this->actingAs($admin)
            ->post(route('admin.payments.verify', $payment))
            ->assertRedirect();

        $payment->refresh();
        $order->refresh();
        $this->assertEquals('verified', $payment->status);
        $this->assertEquals('paid', $order->payment_status);
        $this->assertEquals('ready', $order->order_status);

        $access = UserToolAccess::where('order_id', $order->id)->where('tool_id', $tool->id)->first();
        $this->assertNotNull($access);
        $this->assertNotNull($access->tool_account_id);

        $account = ToolAccount::find($access->tool_account_id);
        $this->assertEquals(1, $account->used_slots);

        // 6. Admin delivers the access
        $this->actingAs($admin)
            ->post(route('admin.accesses.deliver', $access))
            ->assertRedirect();

        $access->refresh();
        $this->assertEquals('delivered', $access->delivery_status);
        $this->assertEquals('active', $access->status);

        // Pages render without errors
        $this->actingAs($customer)->get(route('customer.orders.show', $order))->assertOk();
        $this->actingAs($admin)->get(route('admin.orders.show', $order))->assertOk();
    }

    public function test_package_without_custom_fields_skips_info_step(): void
    {
        Queue::fake();

        $customer = $this->customer();
        $admin = $this->admin();

        $tool = Tool::factory()->create();
        ToolAccount::factory()->create([
            'tool_id' => $tool->id,
            'max_users' => 5,
            'used_slots' => 0,
            'status' => 'active',
        ]);

        $package = Package::factory()->create(['status' => 'active', 'delivery_type' => 'manual']);
        PackageTool::factory()->create(['package_id' => $package->id, 'tool_id' => $tool->id]);

        $this->actingAs($customer)
            ->post(route('customer.orders.store'), ['package_id' => $package->id]);

        $order = Order::where('user_id', $customer->id)->latest()->first();
        $this->assertEquals('not_required', $order->required_info_status);

        // Verify payment only -> ready
        $payment = Payment::factory()->create(['order_id' => $order->id, 'status' => 'pending']);
        $this->actingAs($admin)->post(route('admin.payments.verify', $payment))->assertRedirect();

        $order->refresh();
        $this->assertEquals('ready', $order->order_status);
        $this->assertDatabaseHas('user_tool_accesses', ['order_id' => $order->id]);
    }
}
