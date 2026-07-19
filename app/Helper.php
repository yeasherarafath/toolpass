<?php

namespace App;

use App\Services\Settings;
use Illuminate\Support\Str;

/**
 * Grab-bag of repetitive helpers, mostly thin wrappers around the central
 * platform Settings service plus a few small "fun" utilities used across
 * views and controllers.
 */
class Helper
{
    protected static function settings(): Settings
    {
        return app(Settings::class);
    }

    public static function setting(string $key, mixed $default = null): mixed
    {
        return self::settings()->get($key, $default);
    }

    public static function path(string $key, string $default): string
    {
        return self::settings()->path($key, $default);
    }

    public static function bool(string $key, bool $default = false): bool
    {
        return self::settings()->bool($key, $default);
    }

    public static function siteName(): string
    {
        return (string) self::settings()->get('site_name', 'ToolPass');
    }

    public static function supportEmail(): ?string
    {
        return self::settings()->get('support_email');
    }

    public static function adminPath(): string
    {
        return self::settings()->path('admin_path', 'yatpmin');
    }

    public static function ownerPath(): string
    {
        return self::settings()->path('owner_path', 'business');
    }

    public static function initials(string $name, int $limit = 2): string
    {
        $parts = preg_split('/\s+/', trim($name)) ?: [$name];

        return collect($parts)
            ->filter()
            ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
            ->take($limit)
            ->implode('');
    }

    public static function randomColor(): string
    {
        return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
    }

    public static function mask(string $value, int $keep = 3): string
    {
        $length = mb_strlen($value);

        if ($length <= $keep) {
            return str_repeat('*', $length);
        }

        return mb_substr($value, 0, $keep) . str_repeat('*', $length - $keep);
    }

    public static function slugify(string $value): string
    {
        return Str::slug($value);
    }

    /**
     * Cache an Eloquent collection forever, self-healing on a corrupted or
     * unserializable cache entry (e.g. a stale __PHP_Incomplete_Class left
     * behind by an earlier autoloader/config state).
     *
     * @param  (callable(): \Illuminate\Database\Eloquent\Collection)  $callback
     */
    public static function cachedCollection(string $key, callable $callback): \Illuminate\Database\Eloquent\Collection
    {
        $value = \Illuminate\Support\Facades\Cache::get($key);

        if ($value instanceof \Illuminate\Database\Eloquent\Collection) {
            return $value;
        }

        \Illuminate\Support\Facades\Cache::forget($key);

        return \Illuminate\Support\Facades\Cache::rememberForever($key, $callback);
    }
}
