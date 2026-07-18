<?php

namespace App\Services;

use App\Enum\CacheKeyEnum;
use App\Models\PlatformSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

/**
 * Central platform settings (central DB, key-value store).
 *
 * Reads/writes App\Models\PlatformSetting with a cached full map. Values flagged
 * is_encrypted are transparently encrypted on write and decrypted on read.
 * This service is for GLOBAL/platform config only. For per-business (tenant)
 * settings use App\Services\TenantSettings.
 */
class Settings
{
    protected const CACHE_KEY = CacheKeyEnum::PLATFORM_SETTINGS->value;

    /**
     * @return array<string, mixed>
     */
    protected function map(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            return PlatformSetting::query()->get()->mapWithKeys(function (PlatformSetting $setting) {
                $value = $setting->value;

                if ($setting->is_encrypted && ! is_null($value)) {
                    try {
                        $value = Crypt::decryptString($value);
                    } catch (\Throwable $e) {
                        $value = null;
                    }
                }

                return [$setting->key => $value];
            })->all();
        });
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->map()[$key] ?? $default;
    }

    public function bool(string $key, bool $default = false): bool
    {
        $value = $this->get($key, $default);

        return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $default;
    }

    /**
     * Safely resolve a URL path prefix setting for route registration at boot.
     * Reads the cached settings map and never throws (falls back to $default
     * when the table/cache is unavailable, e.g. during install/migrate).
     */
    public function path(string $key, string $default): string
    {
        try {
            $value = $this->get($key, $default);
        } catch (\Throwable $e) {
            return trim($default, '/');
        }

        $value = is_string($value) ? trim($value, '/') : '';

        return $value !== '' ? $value : trim($default, '/');
    }

    /**
     * @return array<string, mixed>
     */
    public function all(?string $group = null): array
    {
        if (is_null($group)) {
            return $this->map();
        }

        $keys = PlatformSetting::query()->where('group', $group)->pluck('key')->all();

        return collect($this->map())->only($keys)->all();
    }

    public function set(string $key, mixed $value, bool $encrypted = false, string $group = 'general'): void
    {
        $stored = $value;

        if ($encrypted && ! is_null($value)) {
            $stored = Crypt::encryptString((string) $value);
        }

        PlatformSetting::updateOrCreate(
            ['key' => $key],
            ['value' => $stored, 'is_encrypted' => $encrypted, 'group' => $group]
        );

        $this->flush();
    }

    public function forget(string $key): void
    {
        PlatformSetting::where('key', $key)->delete();

        $this->flush();
    }

    public function flush(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
