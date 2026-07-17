<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Actions\Platform\OwnerLoginAction;
use App\Actions\Platform\OwnerRegisterAction;
use App\Services\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('platform.auth.login');
    }

    public function showRegister(Settings $settings)
    {
        if (! $settings->bool('allow_owner_registration')) {
            return redirect()->route('platform.login')->withErrors(['email' => 'Registration is currently disabled.']);
        }

        return view('platform.auth.register');
    }

    public function register(Request $request, Settings $settings)
    {
        if (! $settings->bool('allow_owner_registration')) {
            return redirect()->route('platform.login')->withErrors(['email' => 'Registration is currently disabled.']);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:190', 'unique:central.owners,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'business_name' => ['required', 'string', 'max:190'],
            'slug' => ['required', 'string', 'max:60', 'alpha_dash'],
        ]);

        try {
            $owner = app(OwnerRegisterAction::class)($data);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->onlyInput('name', 'email', 'business_name', 'slug');
        }

        if ($owner->status === 'pending' || is_null($owner->email_verified_at)) {
            return redirect()->route('platform.login')->with('status', 'Your account has been created and is pending activation.');
        }

        Auth::guard('owner')->login($owner);
        $request->session()->regenerate();

        return redirect()->route('platform.dashboard');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        try {
            app(OwnerLoginAction::class)($credentials, $request->boolean('remember'));
        } catch (\Throwable $e) {
            return back()->withErrors(['email' => $e->getMessage()])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('platform.dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::guard('owner')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('platform.login');
    }
}
