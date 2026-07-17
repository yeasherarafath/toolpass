<?php

namespace Tests\Feature;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\Package;
use App\Models\PackageTool;
use App\Models\Tool;
use App\Models\UserToolAccess;
use App\Models\User;
use App\Models\Review;
use App\Models\Wallet;
use App\Actions\Wallet\CreditWalletAction;
use App\Actions\Wallet\DebitWalletAction;
use App\Actions\Orders\CreateRenewalAction;
use App\Actions\Review\CreateReviewAction;
use App\Actions\Review\ModerateReviewAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TenantTestCase;

class AddNowFeaturesTest extends TenantTestCase
{
    use RefreshDatabase;

    public function test_applying_coupon_reduces_payable_and_records_usage(): void
    {
        Queue::fake();
        $customer = User::factory()->create(['role' => 'customer']);
        $order = Order::factory()->create([
            'user_id' => $customer->id,
            'payment_status' => 'pending',
            'amount' => 100,
            'payable_amount' => 100,
        ]);
        $coupon = Coupon::factory()->create([
            'code' => 'SAVE10',
            'type' => 'percent',
            'value' => 10,
            'min_amount' => 50,
            'max_discount' => null,
            'status' => 'active',
        ]);

        $this->actingAs($customer)
            ->post(route('customer.orders.coupon', $order), ['code' => 'SAVE10'])
            ->assertRedirect();

        $order->refresh();
        $coupon->refresh();
        $this->assertEquals(90, (float) $order->payable_amount);
        $this->assertEquals(10, (float) $order->discount_amount);
        $this->assertEquals(1, $coupon->used_count);
        $this->assertDatabaseHas('coupon_usages', ['coupon_id' => $coupon->id, 'order_id' => $order->id]);
    }

    public function test_coupon_rejected_when_minimum_not_met(): void
    {
        Queue::fake();
        $customer = User::factory()->create(['role' => 'customer']);
        $order = Order::factory()->create([
            'user_id' => $customer->id,
            'payment_status' => 'pending',
            'payable_amount' => 20,
        ]);
        $coupon = Coupon::factory()->create([
            'code' => 'BIG',
            'type' => 'fixed',
            'value' => 5,
            'min_amount' => 50,
            'status' => 'active',
        ]);

        $this->actingAs($customer)
            ->post(route('customer.orders.coupon', $order), ['code' => 'BIG'])
            ->assertRedirect()
            ->assertSessionHasErrors('coupon');

        $order->refresh();
        $this->assertEquals(20, (float) $order->payable_amount);
    }

    public function test_wallet_credit_and_debit(): void
    {
        Queue::fake();
        $user = User::factory()->create();

        app(CreditWalletAction::class)->handle($user, 'BDT', 50, 'topup');
        app(DebitWalletAction::class)->handle($user, 'BDT', 30, 'spend');

        $wallet = Wallet::where('user_id', $user->id)->where('currency', 'BDT')->first();
        $this->assertEquals(20, (float) $wallet->balance);
        $this->assertDatabaseCount('wallet_transactions', 2);

        $this->expectException(\RuntimeException::class);
        app(DebitWalletAction::class)->handle($user, 'BDT', 100, 'too much');
    }

    public function test_trial_order_renews_to_paid_order(): void
    {
        Queue::fake();
        $customer = User::factory()->create(['role' => 'customer']);
        $package = Package::factory()->create(['status' => 'active']);
        $trial = Order::factory()->create([
            'user_id' => $customer->id,
            'package_id' => $package->id,
            'is_trial' => true,
            'payment_status' => 'paid',
        ]);

        $renewal = app(CreateRenewalAction::class)->handle($trial, $customer);

        $this->assertFalse($renewal->is_trial);
        $this->assertEquals($trial->id, $renewal->renewed_from_order_id);
        $this->assertEquals('pending', $renewal->payment_status);
    }

    public function test_review_requires_delivered_access_and_can_be_moderated(): void
    {
        Queue::fake();
        $customer = User::factory()->create(['role' => 'customer']);
        $admin = User::factory()->admin()->create();
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
            'delivery_status' => 'delivered',
        ]);

        $review = app(CreateReviewAction::class)->handle($customer, $package, [
            'rating' => 5,
            'title' => 'Great',
            'body' => 'Loved it',
        ]);
        $this->assertEquals('pending', $review->status);

        app(ModerateReviewAction::class)->handle($review, 'approve', $admin);
        $review->refresh();
        $this->assertEquals('approved', $review->status);
        $this->assertDatabaseHas('reviews', ['id' => $review->id, 'status' => 'approved']);
    }

    public function test_review_blocked_without_access(): void
    {
        Queue::fake();
        $customer = User::factory()->create(['role' => 'customer']);
        $package = Package::factory()->create(['status' => 'active']);

        $this->expectException(\RuntimeException::class);
        app(CreateReviewAction::class)->handle($customer, $package, ['rating' => 4, 'title' => 'x']);
    }
}
