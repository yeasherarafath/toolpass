<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Actions\Auth\LoginAction;
use App\Actions\Auth\RegisterCustomerAction;
use App\Actions\Auth\LogoutAction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        try {
            $user = app(LoginAction::class)($credentials, $request->boolean('remember'));
        } catch (\Throwable $e) {
            return back()->withErrors(['email' => $e->getMessage()])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(route($user->role . '.dashboard'));
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = app(RegisterCustomerAction::class)($data);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('customer.dashboard');
    }

    public function logout(Request $request)
    {
        app(LogoutAction::class)();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
