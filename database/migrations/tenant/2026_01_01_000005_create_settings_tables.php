<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();

            $table->string('key', 150)->unique()->comment('Setting key, for example business_name');
            $table->text('value')->nullable()->comment('Setting value (encrypted for secrets)');
            $table->boolean('is_encrypted')->default(false)->comment('1 if value is encrypted');
            $table->string('group', 50)->default('business')->comment('business, branding, general');

            $table->timestamps();

            $table->index('group', 'idx_settings_group');
            $table->comment('Per-tenant (per-business) key-value settings');
        });

        Schema::create('offer_banners', function (Blueprint $table) {
            $table->id();

            $table->string('title', 190)->comment('Banner title/heading');
            $table->text('description')->nullable()->comment('Banner description text');
            $table->string('image_path', 255)->nullable()->comment('Banner image path on public disk');
            $table->string('link', 255)->nullable()->comment('Optional target URL');
            $table->unsignedInteger('sort_order')->default(0)->comment('Display order');
            $table->string('status', 30)->default('active')->comment('active, inactive');

            $table->timestamps();

            $table->index('status', 'idx_offer_banners_status');
            $table->comment('Per-tenant storefront offer/promo banners');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offer_banners');
        Schema::dropIfExists('settings');
    }
};
