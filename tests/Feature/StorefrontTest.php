<?php

namespace Tests\Feature;

use App\Models\Package;
use App\Models\Tool;
use App\Models\PackageTool;
use App\Services\CachePatternService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TenantTestCase;

class StorefrontTest extends TenantTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app(CachePatternService::class)->clearByPatterns([
            'storefront:packages:*',
            'storefront:banners:*',
        ]);
    }

    public function test_active_packages_are_listed(): void
    {
        $active = Package::factory()->create(['status' => 'active']);
        $inactive = Package::factory()->create(['status' => 'inactive']);

        $response = $this->get(route('store.index'));

        $response->assertOk();
        $response->assertSee($active->name);
        $response->assertDontSee($inactive->name);
    }

    public function test_inactive_package_is_not_viewable(): void
    {
        $package = Package::factory()->create(['status' => 'inactive']);

        $this->get(route('store.show', $package))->assertNotFound();
    }

    public function test_active_package_show_lists_tools(): void
    {
        $tool = Tool::factory()->create();
        $package = Package::factory()->create(['status' => 'active']);
        PackageTool::factory()->create(['package_id' => $package->id, 'tool_id' => $tool->id]);

        $this->get(route('store.show', $package))
            ->assertOk()
            ->assertSee($tool->name);
    }
}
