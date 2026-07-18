<?php

namespace App\Enum;

use App\Services\Settings;

enum CacheKeyEnum: string
{
    /**
     * ═══════════════════════════════════════════════════════════════
     * IMPORTANT — Read before adding a new cache key:
     *
     * 1. ADD your case here.
     * 2. REGISTER it in group()  — assigns to a top-level group.
     * 3. REGISTER it in subModule() — assigns to a sub-module.
     * 4. REGISTER it in structure() — adds label, icon, and patterns.
     *
     * If you skip step 2–4, the key will NOT appear in the
     * cache management UI and the validation test will fail.
     * ═══════════════════════════════════════════════════════════════
     */
    case PLATFORM_SETTINGS = 'platform.settings.all';

    case TENANT_SETTINGS_PREFIX = 'tenant:';

    case ADMIN_DASHBOARD = 'admin_dashboard_widgets';

    case CUSTOMER_DASHBOARD_PREFIX = 'customer_dashboard:';

    case OTP_RATE_PREFIX = 'otp_rate:';

    case ADMIN_DASHBOARD_WIDGETS = 'platform.admin_dashboard_widgets';

    case OWNER_DASHBOARD_WIDGETS = 'platform.owner_dashboard_widgets';

    case PLATFORM_DASHBOARD_WIDGETS = 'platform.dashboard_widgets';

    case ADMIN_ROLES_LIST = 'platform.admin_roles';

    case ADMIN_PERMISSIONS_LIST = 'platform.admin_permissions';

    // ────────────────────────────────────────────────────────────
    // Helpers for dynamically-generated keys
    // ────────────────────────────────────────────────────────────

    public static function tenantSettings(string $tenantId): string
    {
        return self::TENANT_SETTINGS_PREFIX->value.$tenantId.'.settings';
    }

    public static function customerDashboard(int|string $userId): string
    {
        return self::CUSTOMER_DASHBOARD_PREFIX->value.$userId;
    }

    public static function otpRate(int|string $userId): string
    {
        return self::OTP_RATE_PREFIX->value.$userId;
    }

    /**
     * Generic model cache key used by the ModelCache trait.
     */
    public static function modelCacheKey(
        string $table,
        string $colKey,
        string $cacheId,
        bool $withRelations = false,
        bool|string $aggregateRelations = false
    ): string {
        return $table
            ."_{$colKey}_{$cacheId}"
            .($withRelations ? '_with_relations' : '')
            .($aggregateRelations ? '_aggregate_'.(is_string($aggregateRelations) ? $aggregateRelations : 'relations') : '');
    }

    // ────────────────────────────────────────────────────────────
    // Module-wise Cache Management (for CacheController UI)
    // ────────────────────────────────────────────────────────────

    /**
     * Map this cache key to a top-level group slug.
     *
     * Groups: settings, tenant, dashboards, admin, other
     */
    public function group(): string
    {
        return match ($this) {
            self::PLATFORM_SETTINGS,
            self::ADMIN_DASHBOARD => 'settings',

            self::ADMIN_ROLES_LIST,
            self::ADMIN_PERMISSIONS_LIST => 'admin',

            self::TENANT_SETTINGS_PREFIX,
            self::CUSTOMER_DASHBOARD_PREFIX => 'tenant',

            self::ADMIN_DASHBOARD_WIDGETS,
            self::OWNER_DASHBOARD_WIDGETS,
            self::PLATFORM_DASHBOARD_WIDGETS => 'dashboards',

            self::OTP_RATE_PREFIX => 'other',
        };
    }

    /**
     * Map this cache key to a sub-module slug within its group.
     */
    public function subModule(): string
    {
        return match ($this) {
            self::PLATFORM_SETTINGS => 'platform',
            self::ADMIN_DASHBOARD => 'admin-dashboard',

            self::ADMIN_ROLES_LIST => 'roles',
            self::ADMIN_PERMISSIONS_LIST => 'permissions',

            self::TENANT_SETTINGS_PREFIX => 'tenant-settings',
            self::CUSTOMER_DASHBOARD_PREFIX => 'customer-dashboard',

            self::ADMIN_DASHBOARD_WIDGETS => 'admin',
            self::OWNER_DASHBOARD_WIDGETS => 'owner',
            self::PLATFORM_DASHBOARD_WIDGETS => 'platform',

            self::OTP_RATE_PREFIX => 'otp-rate-limit',
        };
    }

    /**
     * Define the complete cache module hierarchy for the cache management UI.
     *
     * This is the SINGLE SOURCE OF TRUTH for what appears on the
     * cache management page. The CacheController reads this and
     * renders one card per group with per-sub-module clear buttons.
     *
     * ── Patterns ─────────────────────────────────────────────────
     *
     * Patterns are used when cache keys are generated at runtime
     * with variable suffixes (e.g. tenant:{id}.settings). Use
     * Redis-glob syntax (* for any characters, ? for single).
     *
     * The CachePatternService resolves the active cache driver and
     * uses the best strategy:
     *   redis    → KEYS {prefix}{pattern} → forget each
     *   database → DELETE FROM cache WHERE key LIKE {prefix}{pattern}
     *   file     → (not supported — only static keys cleared)
     *   other    → fallback — only static keys cleared, warning logged
     *
     * @return array<string, array> Group-keyed hierarchy.
     */
    public static function structure(): array
    {
        return [
            'settings' => [
                'label' => 'Settings',
                'icon' => 'ti ti-settings',
                'description' => 'Platform & admin dashboard caches.',
                'subModules' => [
                    'platform' => [
                        'label' => 'Platform Settings',
                        'description' => 'Global platform settings map (cached forever until changed).',
                        'icon' => 'ti ti-adjustments',
                        'keys' => [self::PLATFORM_SETTINGS],
                        'patterns' => [],
                    ],
                    'admin-dashboard' => [
                        'label' => 'Admin Dashboard',
                        'description' => 'Cached admin dashboard widget counts.',
                        'icon' => 'ti ti-layout-dashboard',
                        'keys' => [self::ADMIN_DASHBOARD],
                        'patterns' => [],
                    ],
                ],
            ],
            'admin' => [
                'label' => 'Admin / RBAC',
                'icon' => 'ti ti-shield',
                'description' => 'Admin roles and permission lists used in forms.',
                'subModules' => [
                    'roles' => [
                        'label' => 'Admin Roles',
                        'description' => 'Cached admin-guard role list (form selects).',
                        'icon' => 'ti ti-user-tag',
                        'keys' => [self::ADMIN_ROLES_LIST],
                        'patterns' => [],
                    ],
                    'permissions' => [
                        'label' => 'Permissions',
                        'description' => 'Cached admin-guard permission list (form selects).',
                        'icon' => 'ti ti-lock',
                        'keys' => [self::ADMIN_PERMISSIONS_LIST],
                        'patterns' => [],
                    ],
                ],
            ],
            'tenant' => [
                'label' => 'Tenant / Business',
                'icon' => 'ti ti-building',
                'description' => 'Per-tenant settings and customer dashboard caches.',
                'subModules' => [
                    'tenant-settings' => [
                        'label' => 'Tenant Settings',
                        'description' => 'Per-tenant business settings (tenant:{id}.settings).',
                        'icon' => 'ti ti-building-store',
                        'keys' => [self::TENANT_SETTINGS_PREFIX],
                        'patterns' => ['tenant:*'],
                    ],
                    'customer-dashboard' => [
                        'label' => 'Customer Dashboard',
                        'description' => 'Per-customer dashboard widgets (customer_dashboard:{id}).',
                        'icon' => 'ti ti-user',
                        'keys' => [self::CUSTOMER_DASHBOARD_PREFIX],
                        'patterns' => ['customer_dashboard:*'],
                    ],
                ],
            ],
            'dashboards' => [
                'label' => 'Dashboards',
                'icon' => 'ti ti-layout-dashboard',
                'description' => 'Platform, owner, and business dashboard widget caches.',
                'subModules' => [
                    'admin' => [
                        'label' => 'Admin Dashboard',
                        'description' => 'GOD admin dashboard counts (admins, owners, tenants, subscriptions).',
                        'icon' => 'ti ti-shield',
                        'keys' => [self::ADMIN_DASHBOARD_WIDGETS],
                        'patterns' => [],
                    ],
                    'owner' => [
                        'label' => 'Owner Dashboard',
                        'description' => 'Business owner dashboard counts (plans, active subscriptions).',
                        'icon' => 'ti ti-user',
                        'keys' => [self::OWNER_DASHBOARD_WIDGETS],
                        'patterns' => [],
                    ],
                    'platform' => [
                        'label' => 'Platform Dashboard',
                        'description' => 'Landing/platform dashboard counts (tenants, owners, plans, subscriptions).',
                        'icon' => 'ti ti-layout-grid',
                        'keys' => [self::PLATFORM_DASHBOARD_WIDGETS],
                        'patterns' => [],
                    ],
                ],
            ],
            'other' => [
                'label' => 'Other',
                'icon' => 'ti ti-dots',
                'description' => 'Miscellaneous transient caches.',
                'subModules' => [
                    'otp-rate-limit' => [
                        'label' => 'OTP Rate Limit',
                        'description' => 'Per-user OTP submission rate-limit counters.',
                        'icon' => 'ti ti-hourglass',
                        'keys' => [self::OTP_RATE_PREFIX],
                        'patterns' => ['otp_rate:*'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Validate that every enum case is registered in structure().
     *
     * @return array{passed: bool, missing: string[], message: string}
     */
    public static function validateStructure(): array
    {
        $structure = self::structure();
        $registered = [];

        foreach ($structure as $group => $groupData) {
            foreach ($groupData['subModules'] ?? [] as $module) {
                foreach ($module['keys'] ?? [] as $case) {
                    $registered[$case->value] = true;
                }
            }
        }

        $missing = [];

        foreach (self::cases() as $case) {
            if (! isset($registered[$case->value])) {
                $missing[] = $case->name.' ('.$case->value.')';
            }
        }

        if (empty($missing)) {
            return [
                'passed' => true,
                'missing' => [],
                'message' => 'All '.count(self::cases()).' cache keys are registered in structure().',
            ];
        }

        return [
            'passed' => false,
            'missing' => $missing,
            'message' => count($missing).' cache key(s) missing from structure(): '.implode('; ', $missing),
        ];
    }
}
