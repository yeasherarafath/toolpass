<?php

namespace App\Http\Controllers\Platform;

use App\Enum\CacheKeyEnum;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Cache::rememberForever(CacheKeyEnum::ADMIN_ROLES_LIST->value, function () {
            return Role::query()->where('guard_name', 'admin')->orderBy('name')->get();
        });

        return view('platform.admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = $this->permissionsList();

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

        $this->flushRoleCaches();

        return redirect()->route('admin.roles.index')->with('status', 'Role created.');
    }

    public function edit(Role $role)
    {
        $role->load('permissions');
        $permissions = $this->permissionsList();

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

        $this->flushRoleCaches();

        return redirect()->route('admin.roles.index')->with('status', 'Role updated.');
    }

    public function destroy(Role $role)
    {
        $role->delete();

        $this->flushRoleCaches();

        return redirect()->route('admin.roles.index')->with('status', 'Role deleted.');
    }

    protected function permissionsList(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::rememberForever(CacheKeyEnum::ADMIN_PERMISSIONS_LIST->value, function () {
            return Permission::query()->where('guard_name', 'admin')->orderBy('name')->get();
        });
    }

    protected function flushRoleCaches(): void
    {
        Cache::forget(CacheKeyEnum::ADMIN_ROLES_LIST->value);
        Cache::forget(CacheKeyEnum::ADMIN_PERMISSIONS_LIST->value);
    }
}
