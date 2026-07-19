<?php

namespace App\Providers\Platform;

use App\Models\Admin;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

/**
 * Opens every gate/ability for the first platform admin (the Admin with the
 * lowest id, guard_name = "admin"). This is the GOD/super-admin bypass: all
 * `can:` middleware and `->can()` checks short-circuit to true for that single
 * user. Owners and other guards are never touched.
 */
class SuperAdminPermissionProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::before(function ($user, string $ability) {
            if (! $user instanceof Admin) {
                return null;
            }

            $firstAdminId = Admin::query()->min('id');

            return $user->getKey() === $firstAdminId ? true : null;
        });
    }
}
