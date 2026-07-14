<?php

namespace Tests\Unit;

use App\Actions\Access\CreateOrderAccessesAction;
use App\Actions\Concerns\SyncsAccountSlots;
use App\Actions\Orders\MarkOrderReadyAction;
use App\Models\Order;
use App\Models\Package;
use App\Models\PackageTool;
use App\Models\Tool;
use App\Models\ToolAccount;
use App\Models\UserToolAccess;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class OrderReadinessTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_becomes_ready_when_paid_and_approved(): void
    {
        Queue::fake();

        $tool = Tool::factory()->create();
        $package = Package::factory()->create(['status' => 'active']);
        PackageTool::factory()->create(['package_id' => $package->id, 'tool_id' => $tool->id]);

        $order = Order::factory()->create([
            'package_id' => $package->id,
            'payment_status' => 'paid',
            'required_info_status' => 'approved',
            'order_status' => 'pending',
        ]);

        app(MarkOrderReadyAction::class)->handle($order);

        $order->refresh();
        $this->assertEquals('ready', $order->order_status);
        $this->assertDatabaseHas('user_tool_accesses', ['order_id' => $order->id, 'tool_id' => $tool->id]);
    }

    public function test_order_stays_pending_when_unpaid(): void
    {
        Queue::fake();

        $package = Package::factory()->create(['status' => 'active']);
        $order = Order::factory()->create([
            'package_id' => $package->id,
            'payment_status' => 'pending',
            'required_info_status' => 'approved',
            'order_status' => 'pending',
        ]);

        app(MarkOrderReadyAction::class)->handle($order);

        $order->refresh();
        $this->assertEquals('pending', $order->order_status);
        $this->assertDatabaseMissing('user_tool_accesses', ['order_id' => $order->id]);
    }

    public function test_access_creation_is_idempotent(): void
    {
        Queue::fake();

        $tool = Tool::factory()->create();
        $package = Package::factory()->create(['status' => 'active']);
        PackageTool::factory()->create(['package_id' => $package->id, 'tool_id' => $tool->id]);

        $order = Order::factory()->create([
            'package_id' => $package->id,
            'payment_status' => 'paid',
            'required_info_status' => 'approved',
            'order_status' => 'ready',
        ]);

        app(CreateOrderAccessesAction::class)->handle($order);
        app(CreateOrderAccessesAction::class)->handle($order);

        $this->assertEquals(1, UserToolAccess::where('order_id', $order->id)->count());
    }
}

class AccountSlotSyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_access_assigns_account_and_increments_slot(): void
    {
        $tool = Tool::factory()->create();
        $account = ToolAccount::factory()->create([
            'tool_id' => $tool->id,
            'max_users' => 3,
            'used_slots' => 0,
            'status' => 'active',
        ]);

        $access = UserToolAccess::factory()->create([
            'tool_id' => $tool->id,
            'tool_account_id' => null,
        ]);

        $access->refresh();
        $account->refresh();
        $this->assertEquals($account->id, $access->tool_account_id);
        $this->assertEquals(1, $account->used_slots);
    }

    public function test_revoking_access_frees_slot(): void
    {
        $tool = Tool::factory()->create();
        $account = ToolAccount::factory()->create([
            'tool_id' => $tool->id,
            'max_users' => 3,
            'used_slots' => 0,
            'status' => 'active',
        ]);

        $access = UserToolAccess::factory()->create([
            'tool_id' => $tool->id,
            'tool_account_id' => $account->id,
            'status' => 'active',
            'delivery_status' => 'delivered',
        ]);

        $access->update(['status' => 'revoked']);
        $account->refresh();
        $this->assertEquals(0, $account->used_slots);
    }
}
