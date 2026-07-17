<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\PlatformSetting;
use App\Services\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function __construct(protected Settings $settings)
    {
    }

    public function edit()
    {
        $groups = PlatformSetting::query()
            ->orderBy('group')
            ->get()
            ->groupBy('group');

        return view('platform.settings.edit', [
            'settings' => $this->settings->all(),
            'groups' => $groups,
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            // Branding
            'site_name' => ['nullable', 'string', 'max:150'],
            'site_description' => ['nullable', 'string', 'max:500'],
            'site_keywords' => ['nullable', 'string', 'max:500'],
            'footer_text' => ['nullable', 'string', 'max:255'],
            'support_email' => ['nullable', 'email', 'max:190'],
            'support_phone' => ['nullable', 'string', 'max:30'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'favicon' => ['nullable', 'image', 'max:1024'],
            // Registration
            'allow_owner_registration' => ['nullable', 'boolean'],
            'require_email_verification' => ['nullable', 'boolean'],
            'require_admin_approval' => ['nullable', 'boolean'],
            'default_plan_slug' => ['nullable', 'string', 'max:190'],
            'tenant_domain_suffix' => ['nullable', 'string', 'max:190'],
            // General
            'default_currency' => ['nullable', 'string', 'max:3'],
            'default_timezone' => ['nullable', 'string', 'max:64'],
            'maintenance_mode' => ['nullable', 'boolean'],
            'social_facebook' => ['nullable', 'string', 'max:255'],
            'social_x' => ['nullable', 'string', 'max:255'],
            'social_instagram' => ['nullable', 'string', 'max:255'],
            // Mail
            'mail.from_name' => ['nullable', 'string', 'max:150'],
            'mail.from_address' => ['nullable', 'email', 'max:190'],
            'mail.host' => ['nullable', 'string', 'max:190'],
            'mail.port' => ['nullable', 'string', 'max:10'],
            'mail.username' => ['nullable', 'string', 'max:190'],
            'mail.password' => ['nullable', 'string', 'max:190'],
            'mail.encryption' => ['nullable', 'string', 'max:20'],
            // SMS
            'sms.provider' => ['nullable', 'string', 'max:50'],
            'sms.api_key' => ['nullable', 'string', 'max:255'],
            'sms.sender_id' => ['nullable', 'string', 'max:50'],
        ]);

        $groups = [
            'branding' => ['site_name', 'site_description', 'site_keywords', 'footer_text', 'support_email', 'support_phone'],
            'registration' => ['default_plan_slug', 'tenant_domain_suffix'],
            'general' => ['default_currency', 'default_timezone', 'social_facebook', 'social_x', 'social_instagram'],
            'mail' => ['mail.from_name', 'mail.from_address', 'mail.host', 'mail.port', 'mail.username', 'mail.encryption'],
            'sms' => ['sms.provider', 'sms.sender_id'],
        ];

        foreach ($groups as $group => $keys) {
            foreach ($keys as $key) {
                $this->settings->set($key, $data[$key] ?? null, false, $group);
            }
        }

        $booleans = [
            'registration' => ['allow_owner_registration', 'require_email_verification', 'require_admin_approval'],
            'general' => ['maintenance_mode'],
        ];

        foreach ($booleans as $group => $keys) {
            foreach ($keys as $key) {
                $this->settings->set($key, $request->boolean($key) ? '1' : '0', false, $group);
            }
        }

        $secrets = [
            'mail' => ['mail.password'],
            'sms' => ['sms.api_key'],
        ];

        foreach ($secrets as $group => $keys) {
            foreach ($keys as $key) {
                if (filled($data[$key] ?? null)) {
                    $this->settings->set($key, $data[$key], true, $group);
                }
            }
        }

        if ($request->hasFile('logo')) {
            $this->replaceFile('logo_path', $request->file('logo')->store('platform/branding', 'public'));
        }

        if ($request->hasFile('favicon')) {
            $this->replaceFile('favicon_path', $request->file('favicon')->store('platform/branding', 'public'));
        }

        return back()->with('status', 'Settings updated.');
    }

    protected function replaceFile(string $key, string $newPath): void
    {
        $old = $this->settings->get($key);

        if ($old && Storage::disk('public')->exists($old)) {
            Storage::disk('public')->delete($old);
        }

        $this->settings->set($key, $newPath, false, 'branding');
    }
}
