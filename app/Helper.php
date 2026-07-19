<?php

use App\Services\Settings;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

if (! function_exists('setting')) {
    /**
     * Read a central platform setting.
     */
    function setting(string $key, mixed $default = null): mixed
    {
        return app(Settings::class)->get($key, $default);
    }
}

if (! function_exists('helper_path')) {
    /**
     * Resolve a URL path-prefix setting for route registration.
     */
    function helper_path(string $key, string $default): string
    {
        return app(Settings::class)->path($key, $default);
    }
}

if (! function_exists('helper_bool')) {
    function helper_bool(string $key, bool $default = false): bool
    {
        return app(Settings::class)->bool($key, $default);
    }
}

if (! function_exists('site_name')) {
    function site_name(): string
    {
        return (string) app(Settings::class)->get('site_name', 'ToolPass');
    }
}

if (! function_exists('support_email')) {
    function support_email(): ?string
    {
        return app(Settings::class)->get('support_email');
    }
}

if (! function_exists('admin_path')) {
    function admin_path(): string
    {
        return app(Settings::class)->path('admin_path', 'yatpmin');
    }
}

if (! function_exists('owner_path')) {
    function owner_path(): string
    {
        return app(Settings::class)->path('owner_path', 'business');
    }
}

if (! function_exists('initials')) {
    function initials(string $name, int $limit = 2): string
    {
        $parts = preg_split('/\s+/', trim($name)) ?: [$name];

        return collect($parts)
            ->filter()
            ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
            ->take($limit)
            ->implode('');
    }
}

if (! function_exists('random_color')) {
    function random_color(): string
    {
        return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
    }
}

if (! function_exists('mask')) {
    function mask(string $value, int $keep = 3): string
    {
        $length = mb_strlen($value);

        if ($length <= $keep) {
            return str_repeat('*', $length);
        }

        return mb_substr($value, 0, $keep) . str_repeat('*', $length - $keep);
    }
}

if (! function_exists('slugify')) {
    function slugify(string $value): string
    {
        return Str::slug($value);
    }
}

if (! function_exists('cached_collection')) {
    /**
     * Cache an Eloquent collection forever, self-healing on a corrupted or
     * unserializable cache entry (e.g. a stale __PHP_Incomplete_Class left
     * behind by an earlier autoloader/config state).
     *
     * @param  (callable(): \Illuminate\Database\Eloquent\Collection)  $callback
     */
    function cached_collection(string $key, callable $callback): \Illuminate\Database\Eloquent\Collection
    {
        $value = Cache::get($key);

        if ($value instanceof \Illuminate\Database\Eloquent\Collection) {
            return $value;
        }

        Cache::forget($key);

        return Cache::rememberForever($key, $callback);
    }
}
