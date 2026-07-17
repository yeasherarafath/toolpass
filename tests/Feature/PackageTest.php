<?php

namespace Tests\Feature;

use App\Models\Package;
use App\Models\Tool;
use App\Models\ToolCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TenantTestCase;

class PackageTest extends TenantTestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->admin()->create();
    }

    private function makeTool(): Tool
    {
        return Tool::factory()->create(['category_id' => ToolCategory::factory()->create()->id]);
    }

    public function test_admin_can_create_package_with_tools(): void
    {
        $tool = $this->makeTool();

        $this->actingAs($this->admin())
            ->post(route('admin.packages.store'), [
                'name' => 'SEO Bundle',
                'type' => 'bundle',
                'delivery_type' => 'instant',
                'status' => 'active',
                'price' => 1200,
                'currency' => 'BDT',
                'duration_days' => 30,
                'tools' => [$tool->id],
            ])
            ->assertRedirect(route('admin.packages.index'));

        $package = Package::where('name', 'SEO Bundle')->first();
        $this->assertSame('seo-bundle', $package->slug);
        $this->assertDatabaseHas('package_tools', ['package_id' => $package->id, 'tool_id' => $tool->id]);
    }

    public function test_admin_can_add_custom_field(): void
    {
        $package = Package::factory()->create();

        $this->actingAs($this->admin())
            ->post(route('admin.packages.custom-fields.store', $package), [
                'label' => 'Invite Email',
                'name' => 'invite_email',
                'type' => 'email',
                'status' => 'active',
                'is_required' => '1',
            ])
            ->assertRedirect(route('admin.packages.edit', $package));

        $this->assertDatabaseHas('package_custom_fields', [
            'package_id' => $package->id,
            'name' => 'invite_email',
        ]);
    }

    public function test_admin_can_remove_custom_field(): void
    {
        $package = Package::factory()->create();
        $field = \App\Models\PackageCustomField::factory()->create(['package_id' => $package->id]);

        $this->actingAs($this->admin())
            ->delete(route('admin.packages.custom-fields.destroy', [$package, $field]))
            ->assertRedirect(route('admin.packages.edit', $package));

        $this->assertSoftDeleted('package_custom_fields', ['id' => $field->id]);
    }

    public function test_package_delete_soft_deletes(): void
    {
        $package = Package::factory()->create();

        $this->actingAs($this->admin())
            ->delete(route('admin.packages.destroy', $package))
            ->assertRedirect(route('admin.packages.index'));

        $this->assertSoftDeleted('packages', ['id' => $package->id]);
    }
}
