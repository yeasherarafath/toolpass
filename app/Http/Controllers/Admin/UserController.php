<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Actions\Admin\CreateUserAction;
use App\Actions\Admin\ToggleUserStatusAction;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('role')->orderBy('name')->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'role' => ['required', 'in:admin,staff,customer'],
            'status' => ['required', 'in:active,suspended,banned'],
            'password' => ['required', 'min:8'],
        ]);

        app(CreateUserAction::class)($data);

        return redirect()->route('business.users.index')->with('status', 'User created.');
    }

    public function toggleStatus(Request $request, User $user)
    {
        try {
            $message = app(ToggleUserStatusAction::class)($user, $request->user());
        } catch (\Throwable $e) {
            return back()->withErrors(['status' => $e->getMessage()]);
        }

        return back()->with('status', $message);
    }
}
