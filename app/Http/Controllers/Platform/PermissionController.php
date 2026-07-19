<?php

namespace App\Http\Controllers\Platform;

use App\Enum\CacheKeyEnum;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Cache::rememberForever(CacheKeyEnum::ADMIN_PERMISSIONS_LIST->value, function () {
            return Permission::query()
                ->where('guard_name', 'admin')
                ->orderBy('name')
                ->with('roles')
                ->get();
        });

        $groups = $permissions
            ->groupBy(fn (Permission $permission) => Str::before($permission->name, '-') ?: $permission->name)
            ->sortKeys();

        return view('platform.admin.permissions.index', compact('groups', 'permissions'));
    }
}
