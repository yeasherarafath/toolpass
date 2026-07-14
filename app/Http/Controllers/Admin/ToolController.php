<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Actions\Tool\CreateToolAction;
use App\Actions\Tool\UpdateToolAction;
use App\Actions\Tool\DeleteToolAction;
use App\Models\Tool;
use App\Models\ToolCategory;
use Illuminate\Http\Request;

class ToolController extends Controller
{
    public function index()
    {
        $tools = Tool::with('category')->orderBy('name')->paginate(20);

        return view('admin.tools.index', compact('tools'));
    }

    public function create()
    {
        $categories = ToolCategory::where('status', 'active')->orderBy('name')->get();

        return view('admin.tools.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:tool_categories,id'],
            'name' => ['required', 'string', 'max:150'],
            'slug' => ['nullable', 'string', 'max:150', 'unique:tools,slug'],
            'website_url' => ['nullable', 'url', 'max:255'],
            'logo' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
            'access_type' => ['required', 'in:shared,credential,manual,none'],
            'otp_required' => ['boolean'],
            'otp_type' => ['nullable', 'in:email,sms,authenticator,none'],
            'otp_note' => ['nullable', 'string'],
            'device_restriction_enabled' => ['boolean'],
            'device_limit_type' => ['nullable', 'in:none,per_account,per_user'],
            'default_max_devices' => ['nullable', 'integer', 'min:0'],
            'device_policy_note' => ['nullable', 'string'],
        ]);

        app(CreateToolAction::class)($data);

        return redirect()->route('admin.tools.index')->with('status', 'Tool created.');
    }

    public function edit(Tool $tool)
    {
        $categories = ToolCategory::where('status', 'active')->orderBy('name')->get();

        return view('admin.tools.edit', compact('tool', 'categories'));
    }

    public function update(Request $request, Tool $tool)
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:tool_categories,id'],
            'name' => ['required', 'string', 'max:150'],
            'slug' => ['nullable', 'string', 'max:150', 'unique:tools,slug,' . $tool->id],
            'website_url' => ['nullable', 'url', 'max:255'],
            'logo' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
            'access_type' => ['required', 'in:shared,credential,manual,none'],
            'otp_required' => ['boolean'],
            'otp_type' => ['nullable', 'in:email,sms,authenticator,none'],
            'otp_note' => ['nullable', 'string'],
            'device_restriction_enabled' => ['boolean'],
            'device_limit_type' => ['nullable', 'in:none,per_account,per_user'],
            'default_max_devices' => ['nullable', 'integer', 'min:0'],
            'device_policy_note' => ['nullable', 'string'],
        ]);

        app(UpdateToolAction::class)($tool, $data);

        return redirect()->route('admin.tools.index')->with('status', 'Tool updated.');
    }

    public function destroy(Tool $tool)
    {
        app(DeleteToolAction::class)($tool);

        return redirect()->route('admin.tools.index')->with('status', 'Tool deleted.');
    }
}
