<?php

namespace App\Services;

use App\Enum\CacheKeyEnum;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

/**
 * ═══════════════════════════════════════════════════════════════════
 * IMPORTANT — When you add a new cache key to CacheKeyEnum, you
 * MUST also register it in CacheKeyEnum::structure() (group,
 * subModule, keys/patterns). Otherwise this service cannot clear
 * it, and the management UI will not show it.
 * ═══════════════════════════════════════════════════════════════════
 *
 * CachePatternService — driver-agnostic pattern-based cache clearing.
 *
 * Many cache keys are dynamically generated at runtime
 * (e.g., tenant:{id}.settings, customer_dashboard:{id}, otp_rate:{id}).
 * Forgetting each one individually is not practical. This service
 * resolves the active cache driver and uses the most efficient
 * pattern-matching strategy available.
 *
 * Supported drivers:
 *   redis     → KEYS {prefix}{pattern} → forget each
 *   database  → DELETE FROM cache WHERE key LIKE {pattern}   (active default here)
 *   file      → Flush entire file cache (full directory, once per request)
 *   other     → (not supported — static keys only)
 */
class CachePatternService
{
    /**
     * Tracks whether file driver flush has already run in this request.
     */
    private static bool $fileDriverFlushed = false;

    /**
     * Clear all cache keys matching a wildcard pattern.
     *
     * Pattern format uses Redis-glob syntax:
     *   *  — matches any sequence of characters
     *   ?  — matches any single character
     *
     * Do NOT include the cache prefix — it is prepended automatically.
     *
     * @param  string  $pattern  Redis-glob pattern (e.g. 'tenant:*').
     * @return int Number of keys cleared. 0 if unsupported driver.
     */
    public function clearByPattern(string $pattern): int
    {
        $driver = config('cache.default');

        // The `array` driver cannot match patterns. Fall back to the
        // database store (the canonical queryable cache store in this app)
        // when the default driver is not pattern-capable.
        if (! in_array($driver, ['redis', 'database', 'file'], true)) {
            $driver = 'database';
        }

        $prefix = config('cache.prefix');

        return match ($driver) {
            'redis' => $this->clearByPatternRedis($prefix, $pattern),
            'database' => $this->clearByPatternDatabase($prefix, $pattern),
            'file' => $this->clearByPatternFile($prefix, $pattern),
            default => $this->clearByPatternFallback($prefix, $pattern),
        };
    }

    /**
     * Clear all patterns in a list.
     *
     * @return int Total keys cleared across all patterns.
     */
    public function clearByPatterns(array $patterns): int
    {
        $total = 0;
        foreach ($patterns as $pattern) {
            $total += $this->clearByPattern($pattern);
        }

        return $total;
    }

    /**
     * Forget one or more static cache keys.
     */
    public function forgetKeys(CacheKeyEnum ...$keys): void
    {
        foreach ($keys as $key) {
            cache()->forget($key->value);
        }
    }

    /**
     * Clear a specific sub-module within a group.
     *
     * @param  string  $group  Group slug.
     * @param  string  $subModule  Sub-module slug.
     */
    public function clearForSubModule(string $group, string $subModule): void
    {
        $structure = CacheKeyEnum::structure();
        $module = $structure[$group]['subModules'][$subModule] ?? null;

        if ($module === null) {
            Log::warning("CachePatternService: unknown sub-module [{$group}/{$subModule}]");

            return;
        }

        foreach ($module['keys'] as $case) {
            cache()->forget($case->value);
        }

        $this->clearByPatterns($module['patterns']);
    }

    /**
     * Clear all sub-modules within a group.
     *
     * @param  string  $group  Group slug.
     */
    public function clearForGroup(string $group): void
    {
        $structure = CacheKeyEnum::structure();
        $groupData = $structure[$group] ?? null;

        if ($groupData === null) {
            Log::warning("CachePatternService: unknown group [{$group}]");

            return;
        }

        foreach ($groupData['subModules'] as $slug => $module) {
            $this->clearForSubModule($group, $slug);
        }
    }

    /**
     * Clear ALL cache keys defined in CacheKeyEnum structure, including
     * all patterns. Optionally runs Artisan optimize:clear.
     *
     * @param  bool  $withOptimize  Run optimize:clear.
     */
    public function clearAll(bool $withOptimize = false): void
    {
        foreach (CacheKeyEnum::structure() as $group => $groupData) {
            $this->clearForGroup($group);
        }

        if ($withOptimize) {
            Artisan::call('optimize:clear');
        }
    }

    // ─── Private driver strategies ─────────────────────────────────

    /**
     * Clear by pattern using Redis SCAN (with KEYS fallback).
     */
    private function clearByPatternRedis(string $prefix, string $pattern): int
    {
        $count = 0;

        try {
            $redis = Cache::getRedis();
            $client = $redis->client();

            $it = null;
            $maxIters = 1000;
            $iters = 0;

            do {
                $keys = $client->scan($it, $prefix.$pattern, 200);
                if (is_array($keys)) {
                    foreach ($keys as $key) {
                        $stripped = str_starts_with($key, $prefix)
                            ? substr($key, strlen($prefix))
                            : $key;

                        Cache::forget($stripped);
                        $count++;
                    }
                }
                $iters++;
            } while ($it > 0 && $iters < $maxIters);

            return $count;
        } catch (\Throwable $e) {
            Log::warning('CachePatternService: Redis SCAN failed, falling back to KEYS', [
                'pattern' => $pattern,
                'error' => $e->getMessage(),
            ]);
        }

        try {
            $redis = Cache::getRedis();
            $keys = $redis->keys($prefix.$pattern);

            foreach ($keys as $key) {
                $stripped = str_starts_with($key, $prefix)
                    ? substr($key, strlen($prefix))
                    : $key;

                Cache::forget($stripped);
                $count++;
            }
        } catch (\Throwable $e) {
            Log::warning('CachePatternService: Redis KEYS fallback also failed', [
                'pattern' => $pattern,
                'error' => $e->getMessage(),
            ]);
        }

        return $count;
    }

    /**
     * Clear by pattern using a LIKE query on the cache table.
     */
    private function clearByPatternDatabase(string $prefix, string $pattern): int
    {
        $table = config('cache.stores.database.table', 'cache');
        $connection = config('cache.stores.database.connection') ?: null;

        // $prefix is the global cache prefix (config('cache.prefix')) that
        // Cache::put / Cache::forget use when writing keys to the table.
        $like = str_replace(['*', '?'], ['%', '_'], $prefix.$pattern);

        try {
            return DB::connection($connection)
                ->table($table)
                ->where('key', 'LIKE', $like)
                ->delete();
        } catch (\Throwable $e) {
            Log::warning('CachePatternService: database LIKE delete failed', [
                'pattern' => $pattern,
                'error' => $e->getMessage(),
            ]);

            return 0;
        }
    }

    /**
     * Clear by pattern for the file cache driver (flush once per request).
     */
    private function clearByPatternFile(string $prefix, string $pattern): int
    {
        if (static::$fileDriverFlushed) {
            return 0;
        }

        static::$fileDriverFlushed = true;

        $directory = config('cache.stores.file.path', storage_path('framework/cache/data'));

        if (! is_dir($directory)) {
            return 0;
        }

        try {
            $files = File::allFiles($directory);
            $count = count($files);

            foreach ($files as $file) {
                File::delete($file->getPathname());
            }

            foreach (File::directories($directory) as $subDir) {
                File::deleteDirectory($subDir);
            }

            Log::info('CachePatternService: file driver cache flushed', [
                'pattern' => $pattern,
                'files_deleted' => $count,
            ]);

            return $count;
        } catch (\Throwable $e) {
            Log::warning('CachePatternService: file driver flush failed', [
                'pattern' => $pattern,
                'error' => $e->getMessage(),
            ]);

            return 0;
        }
    }

    /**
     * Fallback for unsupported drivers — pattern clearing is not available.
     */
    private function clearByPatternFallback(string $prefix, string $pattern): int
    {
        Log::info('CachePatternService: pattern clearing not supported for driver ['.config('cache.default').']', [
            'pattern' => $pattern,
        ]);

        return 0;
    }
}
