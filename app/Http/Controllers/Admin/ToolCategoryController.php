<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Actions\Tool\CreateToolCategoryAction;
use App\Actions\Tool\UpdateToolCategoryAction;
use App\Actions\Tool\DeleteToolCategoryAction;
use App\Models\ToolCategory;
use Illuminate\Http\Request;

class ToolCategoryController extends Controller
{
    public function index()
    {
        $categories = ToolCategory::orderBy('sort_order')->orderBy('name')->paginate(20);

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'slug' => ['nullable', 'string', 'max:150', 'unique:tool_categories,slug'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        app(CreateToolCategoryAction::class)($data);

        return redirect()->route('admin.categories.index')->with('status', 'Category created.');
    }

    public function edit(ToolCategory $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, ToolCategory $category)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'slug' => ['nullable', 'string', 'max:150', 'unique:tool_categories,slug,' . $category->id],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        app(UpdateToolCategoryAction::class)($category, $data);

        return redirect()->route('admin.categories.index')->with('status', 'Category updated.');
    }

    public function destroy(ToolCategory $category)
    {
        app(DeleteToolCategoryAction::class)($category);

        return redirect()->route('admin.categories.index')->with('status', 'Category deleted.');
    }
}
