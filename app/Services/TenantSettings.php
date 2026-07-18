<?php

namespace App\Services;

use App\Enum\CacheKeyEnum;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

/**
 * Per-tenant (per-business) settings (tenant DB, key-value store).
 *
 * Reads/writes App\Models\Setting under the currently-initialized tenant. Cache
 * is namespaced per tenant id so switching tenants never leaks values. Must be
 * called within tenant context (after tenancy is initialized).
 */
class TenantSettings
{
    protected function tenantId(): string
    {
        if (function_exists('tenant') && tenant()) {
            return (string) tenant()->getTenantKey();
        }

        return 'central';
    }

    protected function cacheKey(): string
    {
        return CacheKeyEnum::tenantSettings($this->tenantId());
    }

    /**
     * @return array<string, mixed>
     */
    protected function map(): array
    {
        return Cache::rememberForever($this->cacheKey(), function () {
            return Setting::query()->get()->mapWithKeys(function (Setting $setting) {
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
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return $this->map();
    }

    public function set(string $key, mixed $value, bool $encrypted = false, string $group = 'business'): void
    {
        $stored = $value;

        if ($encrypted && ! is_null($value)) {
            $stored = Crypt::encryptString((string) $value);
        }

        Setting::updateOrCreate(
            ['key' => $key],
            ['value' => $stored, 'is_encrypted' => $encrypted, 'group' => $group]
        );

        $this->flush();
    }

    public function flush(): void
    {
        Cache::forget($this->cacheKey());
    }
}
