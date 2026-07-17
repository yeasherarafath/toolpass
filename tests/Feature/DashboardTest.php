<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\Order;
use App\Models\Package;
use App\Models\PackageTool;
use App\Models\Tool;
use App\Models\UserToolAccess;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TenantTestCase;

class DashboardTest extends TenantTestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_shows_widgets(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Pending payments')
            ->assertSee('Revenue');
    }

    public function test_customer_dashboard_shows_delivered_access_and_announcements(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $tool = Tool::factory()->create();
        $package = Package::factory()->create(['status' => 'active', 'delivery_type' => 'manual']);
        PackageTool::factory()->create(['package_id' => $package->id, 'tool_id' => $tool->id]);
        $order = Order::factory()->create([
            'user_id' => $customer->id,
            'package_id' => $package->id,
            'payment_status' => 'paid',
            'order_status' => 'ready',
        ]);
        UserToolAccess::factory()->create([
            'user_id' => $customer->id,
            'order_id' => $order->id,
            'tool_id' => $tool->id,
            'status' => 'active',
            'delivery_status' => 'delivered',
            'delivery_note' => 'Login via the invite link.',
        ]);
        Announcement::factory()->create([
            'title' => 'Scheduled maintenance',
            'message' => 'We will be down at midnight.',
            'visible_to' => 'customers',
        ]);

        $this->actingAs($customer)
            ->get(route('customer.dashboard'))
            ->assertOk()
            ->assertSee($tool->name)
            ->assertSee('Scheduled maintenance');
    }
}
