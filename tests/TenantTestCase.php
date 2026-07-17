<?php

namespace Tests;

use Tests\Concerns\InteractsWithTenancy;

/**
 * Base test case for tests that exercise tenant-scoped routes / models.
 * Auto-initializes a tenant bound to the "localhost" domain so existing
 * route()-based requests resolve within tenant context.
 */
abstract class TenantTestCase extends TestCase
{
    use InteractsWithTenancy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->initTenant();
    }

    protected function tearDown(): void
    {
        $this->tearDownTenancy();
        parent::tearDown();
    }
}
