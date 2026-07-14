<?php

namespace Tests\Feature;

use App\Models\ToolCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Str;
use Tests\TestCase;

class ToolCategoryTest extends TestCase
{
    use RefreshDatabase;

    private function admin()
    {
        return \App\Models\User::factory()->admin()->create();
    }

    public function test_admin_can_create_category(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.categories.store'), [
                'name' => 'Design Tools',
                'status' => 'active',
                'sort_order' => 3,
            ])
            ->assertRedirect(route('admin.categories.index'));

        $this->assertDatabaseHas('tool_categories', ['name' => 'Design Tools']);
    }

    public function test_slug_is_auto_generated(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.categories.store'), [
                'name' => 'SEO Suite',
                'status' => 'active',
            ]);

        $this->assertDatabaseHas('tool_categories', ['slug' => 'seo-suite']);
    }

    public function test_soft_delete_frees_slug_for_reuse_with_suffix(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->post(route('admin.categories.store'), ['name' => 'AutoCat', 'status' => 'active']);
        $category = ToolCategory::where('name', 'AutoCat')->first();
        $this->assertSame('autocat', $category->slug);

        $this->actingAs($admin)
            ->delete(route('admin.categories.destroy', $category));

        // Recreating the same name must not collide with the soft-deleted row.
        $this->actingAs($admin)
            ->post(route('admin.categories.store'), ['name' => 'AutoCat', 'status' => 'active']);

        $this->assertDatabaseHas('tool_categories', ['slug' => 'autocat-1']);
        $this->assertSame(2, ToolCategory::withTrashed()->where('name', 'AutoCat')->count());
    }

    public function test_admin_can_update_category(): void
    {
        $category = ToolCategory::factory()->create(['name' => 'Old', 'slug' => 'old']);

        $this->actingAs($this->admin())
            ->put(route('admin.categories.update', $category), [
                'name' => 'New Name',
                'status' => 'inactive',
            ])
            ->assertRedirect(route('admin.categories.index'));

        $this->assertDatabaseHas('tool_categories', ['id' => $category->id, 'name' => 'New Name', 'slug' => 'new-name']);
    }
}
