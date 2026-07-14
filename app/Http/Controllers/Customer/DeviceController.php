<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Actions\Device\RequestDeviceResetAction;
use App\Models\UserToolAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeviceController extends Controller
{
    public function requestReset(Request $request)
    {
        $validated = $request->validate([
            'user_tool_access_id' => ['required', 'exists:user_tool_accesses,id'],
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $access = UserToolAccess::where('user_id', Auth::id())
            ->findOrFail($validated['user_tool_access_id']);

        try {
            app(RequestDeviceResetAction::class)->handle(
                $access,
                Auth::user(),
                null,
                $validated['reason'] ?? null
            );
        } catch (\Throwable $e) {
            return back()->withErrors(['reset' => $e->getMessage()]);
        }

        return back()->with('success', 'Device reset requested.');
    }
}
