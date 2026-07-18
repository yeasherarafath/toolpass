<?php

namespace App\Http\Controllers\Platform;

use App\Enum\CacheKeyEnum;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    public function index()
    {
        $admins = Admin::query()->orderBy('name')->paginate(25);

        return view('platform.admin.admins.index', compact('admins'));
    }

    public function create()
    {
        $roles = $this->rolesList();

        return view('platform.admin.admins.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:190', 'unique:central.admins,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'string', 'min:8'],
            'status' => ['required', 'in:active,suspended'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['exists:central.roles,id'],
        ]);

        $data['password'] = Hash::make($data['password']);
        $data['email_verified_at'] = now();

        $admin = Admin::create($data);
        $admin->syncRoles(Role::query()->whereIn('id', $data['roles'] ?? [])->get());

        Cache::forget(CacheKeyEnum::ADMIN_ROLES_LIST->value);

        return redirect()->route('admin.admins.index')->with('status', 'Admin created.');
    }

    public function edit(Admin $admin)
    {
        $roles = $this->rolesList();

        return view('platform.admin.admins.edit', compact('admin', 'roles'));
    }

    public function update(Request $request, Admin $admin)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:190', 'unique:central.admins,email,' . $admin->getKey()],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['nullable', 'string', 'min:8'],
            'status' => ['required', 'in:active,suspended'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['exists:central.roles,id'],
        ]);

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $admin->update($data);
        $admin->syncRoles(Role::query()->whereIn('id', $data['roles'] ?? [])->get());

        Cache::forget(CacheKeyEnum::ADMIN_ROLES_LIST->value);

        return redirect()->route('admin.admins.index')->with('status', 'Admin updated.');
    }

    public function destroy(Admin $admin)
    {
        if ($admin->getKey() === auth('admin')->id()) {
            return back()->withErrors(['email' => 'You cannot delete your own account.']);
        }

        $admin->delete();

        Cache::forget(CacheKeyEnum::ADMIN_ROLES_LIST->value);

        return redirect()->route('admin.admins.index')->with('status', 'Admin deleted.');
    }

    protected function rolesList(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::rememberForever(CacheKeyEnum::ADMIN_ROLES_LIST->value, function () {
            return Role::query()->where('guard_name', 'admin')->get();
        });
    }
}
