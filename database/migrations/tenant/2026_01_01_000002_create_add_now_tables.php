<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->char('code', 3)->unique()->comment('ISO 4217 code, for example BDT, USD');
            $table->string('name', 100)->comment('Currency name');
            $table->string('symbol', 10)->nullable()->comment('Symbol, for example ৳, $');
            $table->decimal('rate', 18, 8)->default(1.0)->comment('Exchange rate relative to base currency');
            $table->boolean('is_default')->default(false)->comment('1 means base/default currency');
            $table->string('status', 30)->default('active')->comment('active, inactive');
            $table->timestamps();
            $table->index('code', 'idx_currencies_code');
            $table->index('status', 'idx_currencies_status');
            $table->comment('Stores supported currencies and exchange rates');
        });

        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->string('subject_type', 150)->comment('Model/table, for example packages, announcements');
            $table->unsignedBigInteger('subject_id')->comment('Related record ID');
            $table->string('locale', 10)->comment('Locale code, for example en, bn');
            $table->string('field', 100)->comment('Field name being translated');
            $table->text('value')->nullable()->comment('Translated value');
            $table->timestamps();
            $table->unique(['subject_type', 'subject_id', 'locale', 'field'], 'uq_translation');
            $table->index(['subject_type', 'subject_id'], 'idx_translations_subject');
            $table->index('locale', 'idx_translations_locale');
            $table->comment('Stores localized strings for multilingual support');
        });

        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->char('currency', 3)->default('BDT')->comment('Wallet currency');
            $table->decimal('balance', 12, 2)->default(0.00)->comment('Available balance');
            $table->decimal('locked_balance', 12, 2)->default(0.00)->comment('Balance reserved for pending orders');
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['user_id', 'currency'], 'uq_wallet_user_currency');
            $table->index('user_id', 'idx_wallets_user_id');
            $table->index('currency', 'idx_wallets_currency');
            $table->comment('Stores customer wallet balances');
        });

        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained('wallets')->cascadeOnDelete();
            $table->string('type', 30)->comment('credit, debit');
            $table->decimal('amount', 12, 2)->default(0.00)->comment('Transaction amount');
            $table->decimal('balance_after', 12, 2)->default(0.00)->comment('Wallet balance after this transaction');
            $table->string('ref_type', 50)->nullable()->comment('order, payment, refund, adjustment');
            $table->unsignedBigInteger('ref_id')->nullable()->comment('Related record ID');
            $table->text('note')->nullable()->comment('Human-readable note');
            $table->timestamps();
            $table->index('wallet_id', 'idx_wallet_transactions_wallet_id');
            $table->index('type', 'idx_wallet_transactions_type');
            $table->index(['ref_type', 'ref_id'], 'idx_wallet_transactions_ref');
            $table->comment('Stores wallet credit/debit history');
        });

        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique()->comment('Public coupon code');
            $table->string('type', 30)->default('percent')->comment('percent, fixed');
            $table->decimal('value', 12, 2)->default(0.00)->comment('Percent or fixed amount');
            $table->decimal('min_amount', 12, 2)->default(0.00)->comment('Minimum order amount to apply');
            $table->decimal('max_discount', 12, 2)->nullable()->comment('Maximum discount cap for percent type');
            $table->char('currency', 3)->default('BDT')->comment('Coupon currency');
            $table->timestamp('starts_at')->nullable()->comment('Valid from');
            $table->timestamp('ends_at')->nullable()->comment('Valid until');
            $table->unsignedInteger('usage_limit')->nullable()->comment('Maximum number of uses, NULL = unlimited');
            $table->unsignedInteger('used_count')->default(0)->comment('Times used');
            $table->string('status', 30)->default('active')->comment('active, inactive');
            $table->timestamps();
            $table->softDeletes();
            $table->index('code', 'idx_coupons_code');
            $table->index('status', 'idx_coupons_status');
            $table->index('currency', 'idx_coupons_currency');
            $table->index('ends_at', 'idx_coupons_ends_at');
            $table->comment('Stores promotional coupons');
        });

        Schema::create('coupon_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coupon_id')->constrained('coupons')->cascadeOnDelete();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('discount_amount', 12, 2)->default(0.00)->comment('Discount applied');
            $table->timestamps();
            $table->unique(['coupon_id', 'order_id'], 'uq_coupon_usage');
            $table->index('coupon_id', 'idx_coupon_usages_coupon_id');
            $table->index('order_id', 'idx_coupon_usages_order_id');
            $table->index('user_id', 'idx_coupon_usages_user_id');
            $table->comment('Stores per-order coupon usage');
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('channel', 30)->default('email')->comment('email, sms');
            $table->string('type', 50)->comment('order_paid, access_delivered, otp_ready, expiry_warning, announcement');
            $table->string('subject', 255)->nullable()->comment('Notification subject');
            $table->text('body')->nullable()->comment('Notification body');
            $table->string('status', 30)->default('pending')->comment('pending, sent, failed');
            $table->timestamp('sent_at')->nullable()->comment('When sent');
            $table->text('error')->nullable()->comment('Error message if failed');
            $table->timestamps();
            $table->index('user_id', 'idx_notifications_user_id');
            $table->index('channel', 'idx_notifications_channel');
            $table->index('type', 'idx_notifications_type');
            $table->index('status', 'idx_notifications_status');
            $table->comment('Stores outgoing email/SMS notifications');
        });

        Schema::create('notification_templates', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 100)->unique()->comment('Template key, for example access_delivered');
            $table->string('channel', 30)->default('email')->comment('email, sms');
            $table->string('subject', 255)->nullable()->comment('Template subject with variables');
            $table->text('body')->nullable()->comment('Template body with variables');
            $table->json('variables')->nullable()->comment('Available variable names');
            $table->string('status', 30)->default('active')->comment('active, inactive');
            $table->timestamps();
            $table->index('slug', 'idx_notification_templates_slug');
            $table->index('channel', 'idx_notification_templates_channel');
            $table->index('status', 'idx_notification_templates_status');
            $table->comment('Stores reusable notification templates');
        });

        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('package_id')->constrained('packages')->cascadeOnDelete();
            $table->unsignedTinyInteger('rating')->default(5)->comment('Rating 1-5');
            $table->string('title', 255)->nullable()->comment('Review title');
            $table->text('body')->nullable()->comment('Review body');
            $table->string('status', 30)->default('pending')->comment('pending, approved, rejected');
            $table->timestamps();
            $table->softDeletes();
            $table->index('user_id', 'idx_reviews_user_id');
            $table->index('package_id', 'idx_reviews_package_id');
            $table->index('status', 'idx_reviews_status');
            $table->index('rating', 'idx_reviews_rating');
            $table->comment('Stores customer ratings and reviews');
        });

        Schema::create('package_contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained('packages')->cascadeOnDelete();
            $table->string('section', 50)->default('includes')->comment('includes, faq, highlights, seo');
            $table->string('title', 255)->nullable()->comment('Section title');
            $table->text('body')->nullable()->comment('Section body');
            $table->unsignedInteger('sort_order')->default(0)->comment('Display order');
            $table->string('status', 30)->default('active')->comment('active, inactive');
            $table->timestamps();
            $table->softDeletes();
            $table->index('package_id', 'idx_package_contents_package_id');
            $table->index('section', 'idx_package_contents_section');
            $table->index('status', 'idx_package_contents_status');
            $table->comment('Stores storefront content sections for packages');
        });

        Schema::create('financial_summaries', function (Blueprint $table) {
            $table->id();
            $table->date('summary_date')->comment('Summary date');
            $table->char('currency', 3)->default('BDT')->comment('Currency');
            $table->unsignedInteger('orders_count')->default(0)->comment('Orders created that day');
            $table->decimal('gross_revenue', 14, 2)->default(0.00)->comment('Total payable amount');
            $table->decimal('discounts', 14, 2)->default(0.00)->comment('Total discounts (coupon/wallet)');
            $table->decimal('refunds', 14, 2)->default(0.00)->comment('Total refunds');
            $table->decimal('wallet_used', 14, 2)->default(0.00)->comment('Amount paid via wallet');
            $table->decimal('net_revenue', 14, 2)->default(0.00)->comment('Net recognized revenue');
            $table->timestamps();
            $table->unique(['summary_date', 'currency'], 'uq_financial_summary');
            $table->index('summary_date', 'idx_financial_summaries_date');
            $table->index('currency', 'idx_financial_summaries_currency');
            $table->comment('Stores daily financial summaries populated by queue job');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_summaries');
        Schema::dropIfExists('package_contents');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('notification_templates');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('coupon_usages');
        Schema::dropIfExists('coupons');
        Schema::dropIfExists('wallet_transactions');
        Schema::dropIfExists('wallets');
        Schema::dropIfExists('translations');
        Schema::dropIfExists('currencies');
    }
};
