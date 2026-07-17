<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->char('currency', 3)->default('BDT')->comment('ISO currency code, for example BDT, USD')->after('price');
            $table->boolean('is_trial')->default(false)->comment('1 means this package can be bought as a trial')->after('is_featured');
            $table->unsignedInteger('trial_days')->nullable()->comment('Trial length in days when is_trial = 1')->after('is_trial');
            $table->string('meta_title', 255)->nullable()->comment('SEO meta title')->after('description');
            $table->string('meta_description', 512)->nullable()->comment('SEO meta description')->after('meta_title');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->char('currency', 3)->default('BDT')->comment('ISO currency code')->after('amount');
            $table->index('currency', 'idx_payments_currency');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->char('currency', 3)->default('BDT')->comment('ISO currency code')->after('payable_amount');
            $table->decimal('wallet_amount', 12, 2)->default(0.00)->comment('Amount paid from customer wallet')->after('currency');
            $table->boolean('paid_via_wallet')->default(false)->comment('1 means wallet was used for part/full payment')->after('wallet_amount');
            $table->boolean('is_trial')->default(false)->comment('1 means this order is a trial')->after('paid_via_wallet');
            $table->foreignId('converted_from_trial_order_id')->nullable()->constrained('orders')->nullOnDelete()->after('is_trial');
            $table->foreignId('coupon_id')->nullable()->constrained('coupons')->nullOnDelete()->after('converted_from_trial_order_id');
            $table->string('coupon_code', 50)->nullable()->comment('Applied coupon code for history')->after('coupon_id');
            $table->index('currency', 'idx_orders_currency');
            $table->index('is_trial', 'idx_orders_is_trial');
            $table->index('coupon_id', 'idx_orders_coupon_id');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('idx_orders_coupon_id');
            $table->dropIndex('idx_orders_is_trial');
            $table->dropIndex('idx_orders_currency');
            $table->dropForeign(['converted_from_trial_order_id']);
            $table->dropForeign(['coupon_id']);
            $table->dropColumn(['currency', 'wallet_amount', 'paid_via_wallet', 'is_trial', 'converted_from_trial_order_id', 'coupon_id', 'coupon_code']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex('idx_payments_currency');
            $table->dropColumn('currency');
        });

        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn(['currency', 'is_trial', 'trial_days', 'meta_title', 'meta_description']);
        });
    }
};
