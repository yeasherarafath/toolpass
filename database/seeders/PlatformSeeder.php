<?php

namespace Database\Seeders;

use App\Models\Owner;
use App\Models\Plan;
use App\Models\PlatformSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PlatformSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'manage-owners',
            'manage-plans',
            'manage-subscriptions',
            'manage-gateway',
            'manage-settings',
            'impersonate-tenant',
            'send-mail',
            'view-platform',
        ];

        foreach ($permissions as $name) {
            Permission::findOrCreate($name, 'owner');
        }

        $superAdmin = Role::findOrCreate('super_admin', 'owner');
        $platformStaff = Role::findOrCreate('platform_staff', 'owner');

        $superAdmin->syncPermissions($permissions);
        $platformStaff->syncPermissions(['view-platform', 'send-mail', 'manage-settings']);

        $owner = Owner::updateOrCreate(
            ['email' => 'superadmin@toolpass.test'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'business_name' => 'ToolPass Platform',
                'status' => 'active',
                'tenant_id' => null,
                'email_verified_at' => now(),
            ]
        );

        $owner->syncRoles(['super_admin']);

        Plan::updateOrCreate(
            ['slug' => 'starter'],
            [
                'name' => 'Starter', 'description' => 'Entry plan for small businesses.',
                'price' => 1000, 'currency' => 'BDT', 'billing_cycle' => 'monthly',
                'max_staff' => 2, 'max_customers' => 100, 'max_packages' => 10,
                'email_quota' => 1000, 'sms_quota' => 200,
                'status' => 'active', 'sort_order' => 1,
            ]
        );

        Plan::updateOrCreate(
            ['slug' => 'pro'],
            [
                'name' => 'Pro', 'description' => 'Growth plan with higher limits.',
                'price' => 3000, 'currency' => 'BDT', 'billing_cycle' => 'monthly',
                'max_staff' => 10, 'max_customers' => 1000, 'max_packages' => 50,
                'email_quota' => 10000, 'sms_quota' => 2000,
                'status' => 'active', 'sort_order' => 2,
            ]
        );

        $settings = [
            // Branding
            ['key' => 'site_name', 'group' => 'branding', 'value' => 'ToolPass'],
            ['key' => 'site_description', 'group' => 'branding', 'value' => 'Multi-tenant tool subscription platform.'],
            ['key' => 'site_keywords', 'group' => 'branding', 'value' => 'tools, subscription, saas'],
            ['key' => 'logo_path', 'group' => 'branding', 'value' => null],
            ['key' => 'favicon_path', 'group' => 'branding', 'value' => null],
            ['key' => 'footer_text', 'group' => 'branding', 'value' => 'ToolPass Platform'],
            ['key' => 'support_email', 'group' => 'branding', 'value' => 'support@toolpass.test'],
            ['key' => 'support_phone', 'group' => 'branding', 'value' => null],

            // Registration & access
            ['key' => 'allow_owner_registration', 'group' => 'registration', 'value' => '1'],
            ['key' => 'require_email_verification', 'group' => 'registration', 'value' => '0'],
            ['key' => 'require_admin_approval', 'group' => 'registration', 'value' => '0'],
            ['key' => 'default_plan_slug', 'group' => 'registration', 'value' => 'starter'],
            ['key' => 'tenant_domain_suffix', 'group' => 'registration', 'value' => env('CENTRAL_DOMAIN', 'toolpass.test')],

            // General
            ['key' => 'default_currency', 'group' => 'general', 'value' => 'BDT'],
            ['key' => 'default_timezone', 'group' => 'general', 'value' => 'Asia/Dhaka'],
            ['key' => 'maintenance_mode', 'group' => 'general', 'value' => '0'],
            ['key' => 'social_facebook', 'group' => 'general', 'value' => null],
            ['key' => 'social_x', 'group' => 'general', 'value' => null],
            ['key' => 'social_instagram', 'group' => 'general', 'value' => null],

            // Mail gateway
            ['key' => 'mail.from_name', 'group' => 'mail', 'value' => 'ToolPass'],
            ['key' => 'mail.from_address', 'group' => 'mail', 'value' => 'no-reply@toolpass.test'],
            ['key' => 'mail.host', 'group' => 'mail', 'value' => null],
            ['key' => 'mail.port', 'group' => 'mail', 'value' => '587'],
            ['key' => 'mail.username', 'group' => 'mail', 'value' => null],
            ['key' => 'mail.password', 'group' => 'mail', 'value' => null, 'is_encrypted' => true],
            ['key' => 'mail.encryption', 'group' => 'mail', 'value' => 'tls'],

            // SMS gateway
            ['key' => 'sms.provider', 'group' => 'sms', 'value' => 'log'],
            ['key' => 'sms.api_key', 'group' => 'sms', 'value' => null, 'is_encrypted' => true],
            ['key' => 'sms.sender_id', 'group' => 'sms', 'value' => null],
        ];

        foreach ($settings as $setting) {
            PlatformSetting::updateOrCreate(
                ['key' => $setting['key']],
                [
                    'group' => $setting['group'],
                    'value' => $setting['value'],
                    'is_encrypted' => $setting['is_encrypted'] ?? false,
                ]
            );
        }
    }
}
