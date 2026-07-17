<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::query()->where('guard_name', 'admin')->orderBy('name')->get();

        return view('platform.admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::query()->where('guard_name', 'admin')->orderBy('name')->get();

        return view('platform.admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150', 'unique:central.roles,name'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:central.permissions,id'],
        ]);

        $role = Role::create(['name' => $data['name'], 'guard_name' => 'admin']);
        $role->syncPermissions(Permission::query()->whereIn('id', $data['permissions'] ?? [])->get());

        return redirect()->route('admin.roles.index')->with('status', 'Role created.');
    }

    public function edit(Role $role)
    {
        $permissions = Permission::query()->where('guard_name', 'admin')->orderBy('name')->get();

        return view('platform.admin.roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150', 'unique:central.roles,name,' . $role->getKey()],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:central.permissions,id'],
        ]);

        $role->update(['name' => $data['name']]);
        $role->syncPermissions(Permission::query()->whereIn('id', $data['permissions'] ?? [])->get());

        return redirect()->route('admin.roles.index')->with('status', 'Role updated.');
    }

    public function destroy(Role $role)
    {
        $role->delete();

        return redirect()->route('admin.roles.index')->with('status', 'Role deleted.');
    }
}
