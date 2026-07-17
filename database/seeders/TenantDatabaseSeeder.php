<?php

namespace Database\Seeders;

use App\Models\Currency;
use App\Models\Order;
use App\Models\Package;
use App\Models\PackageCustomField;
use App\Models\PackageTool;
use App\Models\Tool;
use App\Models\ToolAccount;
use App\Models\ToolCategory;
use App\Models\User;
use App\Models\Setting;
use App\Services\Settings;
use Illuminate\Database\Seeder;

class TenantDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedBusinessSettings();

        Currency::updateOrCreate(
            ['code' => 'BDT'],
            ['name' => 'Bangladeshi Taka', 'symbol' => '৳', 'rate' => 1, 'is_default' => true, 'status' => 'active']
        );

        User::factory()->admin()->create();
        User::factory()->staff()->count(2)->create();
        User::factory()->count(5)->create();

        $seo = ToolCategory::create(['name' => 'SEO Tools', 'slug' => 'seo-tools', 'status' => 'active', 'sort_order' => 1]);
        $ai = ToolCategory::create(['name' => 'AI Tools', 'slug' => 'ai-tools', 'status' => 'active', 'sort_order' => 2]);
        $design = ToolCategory::create(['name' => 'Design Tools', 'slug' => 'design-tools', 'status' => 'active', 'sort_order' => 3]);

        $chatgpt = Tool::create([
            'category_id' => $ai->id, 'name' => 'ChatGPT', 'slug' => 'chatgpt', 'website_url' => 'https://chatgpt.com',
            'status' => 'active', 'access_type' => 'credential', 'otp_required' => true, 'otp_type' => 'email',
            'device_restriction_enabled' => true, 'device_limit_type' => 'device', 'default_max_devices' => 5,
        ]);
        $canva = Tool::create([
            'category_id' => $design->id, 'name' => 'Canva Team', 'slug' => 'canva-team', 'website_url' => 'https://canva.com',
            'status' => 'active', 'access_type' => 'invite', 'otp_required' => false,
            'device_restriction_enabled' => false,
        ]);
        $ahrefs = Tool::create([
            'category_id' => $seo->id, 'name' => 'Ahrefs', 'slug' => 'ahrefs', 'website_url' => 'https://ahrefs.com',
            'status' => 'active', 'access_type' => 'manual', 'otp_required' => true, 'otp_type' => 'email',
            'device_restriction_enabled' => true, 'device_limit_type' => 'device', 'default_max_devices' => 3,
        ]);
        $moz = Tool::create([
            'category_id' => $seo->id, 'name' => 'Moz', 'slug' => 'moz', 'website_url' => 'https://moz.com',
            'status' => 'active', 'access_type' => 'credential', 'otp_required' => false,
        ]);
        $semrush = Tool::create([
            'category_id' => $seo->id, 'name' => 'Semrush', 'slug' => 'semrush', 'website_url' => 'https://semrush.com',
            'status' => 'active', 'access_type' => 'credential', 'otp_required' => false,
        ]);

        foreach ([$chatgpt, $canva, $ahrefs, $moz, $semrush] as $tool) {
            ToolAccount::create([
                'tool_id' => $tool->id,
                'name' => $tool->name . ' Account 1',
                'login_email' => 'owner-' . $tool->slug . '1@example.com',
                'login_password_encrypted' => encrypt('seed-secret-' . $tool->slug),
                'subscription_type' => 'monthly',
                'renewal_date' => now()->addDays(20),
                'expires_at' => now()->addDays(25),
                'max_users' => 5,
                'used_slots' => 0,
                'max_devices' => 5,
                'used_devices' => 0,
                'allow_device_reset' => true,
                'device_reset_interval_days' => 7,
                'otp_required' => $tool->otp_required,
                'otp_type' => $tool->otp_type,
                'otp_receiver' => 'owner-' . $tool->slug . '1@example.com',
                'status' => 'active',
            ]);
        }

        $seoBundle = Package::create([
            'name' => 'SEO Bundle - 30 Days', 'slug' => 'seo-bundle-30-days',
            'type' => 'bundle', 'delivery_type' => 'mixed',
            'description' => 'Ahrefs, Moz, Semrush, ChatGPT and Canva Team access for 30 days.',
            'price' => 700, 'duration_days' => 30, 'status' => 'active',
            'sort_order' => 1, 'is_featured' => true, 'currency' => 'BDT',
        ]);

        foreach ([$ahrefs, $moz, $semrush, $chatgpt, $canva] as $tool) {
            PackageTool::create(['package_id' => $seoBundle->id, 'tool_id' => $tool->id]);
        }

        PackageCustomField::create([
            'package_id' => $seoBundle->id, 'label' => 'Invite Email', 'name' => 'invite_email',
            'type' => 'email', 'is_required' => true, 'validation_rules' => 'required|email',
            'sort_order' => 1, 'status' => 'active',
        ]);
        PackageCustomField::create([
            'package_id' => $seoBundle->id, 'label' => 'Full Name', 'name' => 'full_name',
            'type' => 'text', 'is_required' => false, 'validation_rules' => 'nullable',
            'sort_order' => 2, 'status' => 'active',
        ]);

        Order::factory()->count(10)->create();
    }

    protected function seedBusinessSettings(): void
    {
        $businessName = 'My Business';
        if (function_exists('tenant') && tenant()) {
            $businessName = tenant()->business_name ?: $businessName;
        }

        $currency = 'BDT';
        try {
            $currency = app(Settings::class)->get('default_currency', 'BDT') ?: 'BDT';
        } catch (\Throwable $e) {
            // central settings unavailable - keep BDT fallback
        }

        $defaults = [
            ['key' => 'business_name', 'group' => 'business', 'value' => $businessName],
            ['key' => 'business_description', 'group' => 'business', 'value' => null],
            ['key' => 'business_address', 'group' => 'business', 'value' => null],
            ['key' => 'support_email', 'group' => 'business', 'value' => null],
            ['key' => 'support_phone', 'group' => 'business', 'value' => null],
            ['key' => 'logo_path', 'group' => 'branding', 'value' => null],
            ['key' => 'favicon_path', 'group' => 'branding', 'value' => null],
            ['key' => 'default_currency', 'group' => 'general', 'value' => $currency],
            ['key' => 'social_facebook', 'group' => 'general', 'value' => null],
            ['key' => 'social_x', 'group' => 'general', 'value' => null],
            ['key' => 'social_instagram', 'group' => 'general', 'value' => null],
        ];

        foreach ($defaults as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                ['group' => $setting['group'], 'value' => $setting['value'], 'is_encrypted' => false]
            );
        }
    }
}
