<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as BaseAuthenticate;
use Illuminate\Http\Request;

class Authenticate extends BaseAuthenticate
{
    protected function redirectTo(Request $request): ?string
    {
        if (! $request->expectsJson()) {
            $guard = $this->guardFromRoute($request);

            return match ($guard) {
                'admin' => $this->safeRoute('admin.login', '/yatpmin/login'),
                'owner' => $this->safeRoute('owner.login', '/business/login'),
                default => $this->safeRoute('login', '/login'),
            };
        }

        return null;
    }

    protected function safeRoute(string $name, string $fallback): string
    {
        try {
            return route($name);
        } catch (\Throwable $e) {
            return $fallback;
        }
    }

    protected function guardFromRoute(Request $request): ?string
    {
        $middleware = $request->route()?->middleware() ?? [];

        foreach ($middleware as $m) {
            if (str_starts_with($m, 'auth:')) {
                return substr($m, strlen('auth:'));
            }
        }

        return null;
    }
}
