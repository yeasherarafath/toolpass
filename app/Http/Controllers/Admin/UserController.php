<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'role' => $data['role'],
            'status' => $data['status'],
            'password' => Hash::make($data['password']),
        ]);

        return redirect()->route('admin.users.index')->with('status', 'User created.');
    }

    public function toggleStatus(Request $request, User $user)
    {
        if ($user->id === $request->user()->id && $user->role === 'admin') {
            return back()->withErrors(['status' => 'You cannot change your own admin status.']);
        }

        $user->status = $user->status === 'active' ? 'suspended' : 'active';
        $user->save();

        return back()->with('status', 'User status updated.');
    }
}
