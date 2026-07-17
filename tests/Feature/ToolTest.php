<?php

namespace Tests\Feature;

use App\Models\Tool;
use App\Models\ToolCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TenantTestCase;

class ToolTest extends TenantTestCase
{
    use RefreshDatabase;

    private function admin()
    {
        return \App\Models\User::factory()->admin()->create();
    }

    public function test_admin_can_create_tool(): void
    {
        $category = ToolCategory::factory()->create();

        $this->actingAs($this->admin())
            ->post(route('admin.tools.store'), [
                'category_id' => $category->id,
                'name' => 'Semrush',
                'status' => 'active',
                'access_type' => 'credential',
            ])
            ->assertRedirect(route('admin.tools.index'));

        $this->assertDatabaseHas('tools', ['name' => 'Semrush', 'slug' => 'semrush']);
    }

    public function test_admin_can_update_tool(): void
    {
        $category = ToolCategory::factory()->create();
        $tool = Tool::factory()->create(['category_id' => $category->id, 'name' => 'Ahrefs', 'slug' => 'ahrefs']);

        $this->actingAs($this->admin())
            ->put(route('admin.tools.update', $tool), [
                'category_id' => $category->id,
                'name' => 'Ahrefs Pro',
                'status' => 'inactive',
                'access_type' => 'shared',
            ])
            ->assertRedirect(route('admin.tools.index'));

        $this->assertDatabaseHas('tools', ['id' => $tool->id, 'name' => 'Ahrefs Pro', 'slug' => 'ahrefs-pro']);
    }

    public function test_admin_can_delete_tool(): void
    {
        $category = ToolCategory::factory()->create();
        $tool = Tool::factory()->create(['category_id' => $category->id]);

        $this->actingAs($this->admin())
            ->delete(route('admin.tools.destroy', $tool))
            ->assertRedirect(route('admin.tools.index'));

        $this->assertSoftDeleted('tools', ['id' => $tool->id]);
    }
}
