<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionsSeeder extends Seeder
{
    /**
     * Canonical list of admin-guard permissions for the platform.
     * This is the single source of truth consumed by PlatformSeeder
     * (for role assignment) and by the GOD admin permission list UI.
     */
    public static function permissions(): array
    {
        return [
            'manage-admins',
            'manage-owners',
            'manage-plans',
            'manage-subscriptions',
            'manage-tenants',
            'manage-gateway',
            'manage-settings',
            'manage-reserved-slugs',
            'impersonate-tenant',
            'send-mail',
            'view-platform',
        ];
    }

    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        foreach (self::permissions() as $name) {
            Permission::findOrCreate($name, 'admin');
        }
    }
}
