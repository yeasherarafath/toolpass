<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Actions\ToolAccount\CreateToolAccountAction;
use App\Actions\ToolAccount\UpdateToolAccountAction;
use App\Actions\ToolAccount\DeleteToolAccountAction;
use App\Models\Tool;
use App\Models\ToolAccount;
use Illuminate\Http\Request;

class ToolAccountController extends Controller
{
    public function index()
    {
        $accounts = ToolAccount::with('tool')->orderBy('tool_id')->orderBy('name')->paginate(20);

        return view('admin.tool-accounts.index', compact('accounts'));
    }

    public function create()
    {
        $tools = Tool::where('status', 'active')->orderBy('name')->get();

        return view('admin.tool-accounts.create', compact('tools'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tool_id' => ['required', 'exists:tools,id'],
            'name' => ['required', 'string', 'max:150'],
            'login_email' => ['nullable', 'email', 'max:150'],
            'login_password' => ['nullable', 'string'],
            'recovery_email' => ['nullable', 'email', 'max:150'],
            'account_url' => ['nullable', 'url', 'max:255'],
            'subscription_type' => ['nullable', 'string', 'max:100'],
            'max_users' => ['nullable', 'integer', 'min:1'],
            'used_slots' => ['nullable', 'integer', 'min:0'],
            'otp_required' => ['boolean'],
            'otp_type' => ['nullable', 'in:email,sms,authenticator,none'],
            'otp_receiver' => ['nullable', 'string', 'max:150'],
            'two_factor_secret' => ['nullable', 'string'],
            'backup_codes' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive,expired,issue'],
            'notes' => ['nullable', 'string'],
        ]);

        app(CreateToolAccountAction::class)($data);

        return redirect()->route('business.tool-accounts.index')->with('status', 'Tool account created.');
    }

    public function edit(ToolAccount $toolAccount)
    {
        $tools = Tool::where('status', 'active')->orderBy('name')->get();

        return view('admin.tool-accounts.edit', compact('toolAccount', 'tools'));
    }

    public function update(Request $request, ToolAccount $toolAccount)
    {
        $data = $request->validate([
            'tool_id' => ['required', 'exists:tools,id'],
            'name' => ['required', 'string', 'max:150'],
            'login_email' => ['nullable', 'email', 'max:150'],
            'login_password' => ['nullable', 'string'],
            'recovery_email' => ['nullable', 'email', 'max:150'],
            'account_url' => ['nullable', 'url', 'max:255'],
            'subscription_type' => ['nullable', 'string', 'max:100'],
            'max_users' => ['nullable', 'integer', 'min:1'],
            'used_slots' => ['nullable', 'integer', 'min:0'],
            'otp_required' => ['boolean'],
            'otp_type' => ['nullable', 'in:email,sms,authenticator,none'],
            'otp_receiver' => ['nullable', 'string', 'max:150'],
            'two_factor_secret' => ['nullable', 'string'],
            'backup_codes' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive,expired,issue'],
            'notes' => ['nullable', 'string'],
        ]);

        app(UpdateToolAccountAction::class)($toolAccount, $data);

        return redirect()->route('business.tool-accounts.index')->with('status', 'Tool account updated.');
    }

    public function destroy(ToolAccount $toolAccount)
    {
        app(DeleteToolAccountAction::class)($toolAccount);

        return redirect()->route('business.tool-accounts.index')->with('status', 'Tool account deleted.');
    }
}
