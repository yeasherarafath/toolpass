<?php

namespace Tests\Concerns;

use App\Models\Tenant;

trait InteractsWithTenancy
{
    protected ?Tenant $tenant = null;

    /**
     * Create a tenant whose database points at the current (test) database so
     * that all central + tenant tables created by RefreshDatabase are reused,
     * then initialize tenancy for it.
     *
     * The tenant is registered on the "localhost" domain (the default host used
     * by test HTTP requests) so existing route()/get()/post() calls resolve to
     * the tenant without any per-test changes.
     */
    protected function initTenant(string $id = 'testco', string $domain = 'tenant'): Tenant
    {
        $testDatabase = config('database.connections.' . config('database.default') . '.database');

        $tenant = Tenant::create([
            'id' => $id,
            'business_name' => 'Test Co',
            'status' => 'active',
            'tenancy_db_name' => $testDatabase,
        ]);

        $tenant->domains()->create([
            'domain' => $domain,
        ]);

        tenancy()->initialize($tenant);

        $this->tenant = $tenant;

        return $tenant;
    }

    protected function tearDownTenancy(): void
    {
        if (function_exists('tenancy') && tenancy()->initialized) {
            tenancy()->end();
        }
    }
}
