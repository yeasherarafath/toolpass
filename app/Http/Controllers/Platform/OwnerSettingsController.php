<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Owner;
use App\Services\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class OwnerSettingsController extends Controller
{
    public function __construct(protected Settings $settings)
    {
    }

    public function edit()
    {
        $owner = Auth::guard('owner')->user();

        return view('platform.owner.settings.edit', [
            'owner' => $owner,
            'settings' => $this->settings->all(),
        ]);
    }

    public function update(Request $request)
    {
        $owner = Auth::guard('owner')->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'business_name' => ['required', 'string', 'max:190'],
            'current_password' => ['nullable', 'string'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        if (! empty($data['password'])) {
            if (! Hash::check($data['current_password'] ?? '', $owner->password)) {
                return back()->withErrors(['current_password' => 'The current password is incorrect.']);
            }

            $owner->password = Hash::make($data['password']);
        }

        $owner->name = $data['name'];
        $owner->phone = $data['phone'];
        $owner->business_name = $data['business_name'];
        $owner->save();

        return back()->with('status', 'Profile updated.');
    }
}
