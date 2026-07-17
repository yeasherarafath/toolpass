<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();

            $table->string('name', 150)->comment('Plan name, for example Starter, Pro');
            $table->string('slug', 190)->unique()->comment('URL-friendly plan name');
            $table->text('description')->nullable()->comment('Plan description');

            $table->decimal('price', 12, 2)->default(0.00)->comment('Plan price per billing cycle');
            $table->char('currency', 3)->default('BDT')->comment('Plan currency');
            $table->string('billing_cycle', 30)->default('monthly')->comment('monthly, yearly, lifetime');

            $table->unsignedInteger('max_staff')->nullable()->comment('Max staff users; null = unlimited');
            $table->unsignedInteger('max_customers')->nullable()->comment('Max customers; null = unlimited');
            $table->unsignedInteger('max_packages')->nullable()->comment('Max packages; null = unlimited');
            $table->unsignedInteger('email_quota')->nullable()->comment('Emails per cycle; null = unlimited');
            $table->unsignedInteger('sms_quota')->nullable()->comment('SMS per cycle; null = unlimited');

            $table->json('features')->nullable()->comment('Extra feature flags');

            $table->string('status', 30)->default('active')->comment('active, inactive');
            $table->unsignedInteger('sort_order')->default(0)->comment('Display order');

            $table->timestamps();
            $table->softDeletes();

            $table->index('status', 'idx_plans_status');
            $table->comment('Stores SaaS subscription plans for business owners');
        });

        Schema::create('owners', function (Blueprint $table) {
            $table->id();

            $table->string('tenant_id')->nullable()->comment('Related tenant DB id (business)');

            $table->string('name', 150)->comment('Owner full name');
            $table->string('email', 190)->unique()->comment('Owner login email');
            $table->string('phone', 30)->nullable()->comment('Owner phone');
            $table->string('password', 255)->comment('Hashed password');

            $table->string('business_name', 190)->nullable()->comment('Business/brand name');

            $table->string('status', 30)->default('active')->comment('active, suspended, banned');
            $table->timestamp('last_login_at')->nullable()->comment('Last login time');
            $table->timestamp('email_verified_at')->nullable();

            $table->text('notes')->nullable()->comment('Private platform note');
            $table->rememberToken();

            $table->timestamps();
            $table->softDeletes();

            $table->index('tenant_id', 'idx_owners_tenant_id');
            $table->index('status', 'idx_owners_status');
            $table->comment('Stores business owners (tenant login identities)');
        });

        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('owner_id')->constrained('owners')->cascadeOnDelete()->comment('Subscribing owner');
            $table->string('tenant_id')->nullable()->comment('Related tenant DB id');
            $table->foreignId('plan_id')->constrained('plans')->comment('Subscribed plan');

            $table->string('status', 30)->default('active')->comment('active, past_due, cancelled, expired');
            $table->timestamp('starts_at')->nullable()->comment('Subscription start');
            $table->timestamp('ends_at')->nullable()->comment('Subscription end/expiry');

            $table->decimal('amount', 12, 2)->default(0.00)->comment('Charged amount');
            $table->char('currency', 3)->default('BDT')->comment('Currency');

            $table->text('note')->nullable()->comment('Admin note');

            $table->timestamps();
            $table->softDeletes();

            $table->index('owner_id', 'idx_subscriptions_owner_id');
            $table->index('status', 'idx_subscriptions_status');
            $table->comment('Stores owner subscriptions to plans');
        });

        Schema::create('platform_settings', function (Blueprint $table) {
            $table->id();

            $table->string('key', 150)->unique()->comment('Setting key, for example mail.host');
            $table->text('value')->nullable()->comment('Setting value (encrypted for secrets)');
            $table->boolean('is_encrypted')->default(false)->comment('1 if value is encrypted');
            $table->string('group', 50)->default('general')->comment('mail, sms, general');

            $table->timestamps();

            $table->index('group', 'idx_platform_settings_group');
            $table->comment('Stores global platform settings (mail/SMS gateway, etc.)');
        });

        Schema::create('usage_counters', function (Blueprint $table) {
            $table->id();

            $table->string('tenant_id')->comment('Related tenant DB id');
            $table->string('period', 20)->comment('Billing period key, for example 2026-07');

            $table->unsignedInteger('emails_sent')->default(0)->comment('Emails sent this period');
            $table->unsignedInteger('sms_sent')->default(0)->comment('SMS sent this period');

            $table->timestamps();

            $table->unique(['tenant_id', 'period'], 'uq_usage_counter');
            $table->index('tenant_id', 'idx_usage_counters_tenant_id');
            $table->comment('Stores per-tenant usage counters for quota enforcement');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usage_counters');
        Schema::dropIfExists('platform_settings');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('owners');
        Schema::dropIfExists('plans');
    }
};
