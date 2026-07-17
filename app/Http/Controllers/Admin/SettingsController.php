<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\TenantSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function __construct(protected TenantSettings $settings)
    {
    }

    public function edit()
    {
        return view('admin.settings.edit', [
            'settings' => $this->settings->all(),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'business_name' => ['required', 'string', 'max:190'],
            'business_description' => ['nullable', 'string', 'max:1000'],
            'business_address' => ['nullable', 'string', 'max:500'],
            'support_email' => ['nullable', 'email', 'max:190'],
            'support_phone' => ['nullable', 'string', 'max:30'],
            'default_currency' => ['nullable', 'string', 'max:3'],
            'social_facebook' => ['nullable', 'string', 'max:255'],
            'social_x' => ['nullable', 'string', 'max:255'],
            'social_instagram' => ['nullable', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'favicon' => ['nullable', 'image', 'max:1024'],
        ]);

        $groups = [
            'business' => ['business_name', 'business_description', 'business_address', 'support_email', 'support_phone'],
            'general' => ['default_currency', 'social_facebook', 'social_x', 'social_instagram'],
        ];

        foreach ($groups as $group => $keys) {
            foreach ($keys as $key) {
                $this->settings->set($key, $data[$key] ?? null, false, $group);
            }
        }

        if ($request->hasFile('logo')) {
            $this->replaceFile('logo_path', $request->file('logo'));
        }

        if ($request->hasFile('favicon')) {
            $this->replaceFile('favicon_path', $request->file('favicon'));
        }

        return back()->with('status', 'Business settings updated.');
    }

    protected function replaceFile(string $key, $file): void
    {
        $old = $this->settings->get($key);

        if ($old && Storage::disk('public')->exists($old)) {
            Storage::disk('public')->delete($old);
        }

        $dir = 'tenants/' . (tenant() ? tenant()->getTenantKey() : 'shared') . '/branding';
        $path = $file->store($dir, 'public');

        $this->settings->set($key, $path, false, 'branding');
    }
}
