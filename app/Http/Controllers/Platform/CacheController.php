<?php

namespace App\Http\Controllers\Platform;

use App\Enum\CacheKeyEnum;
use App\Http\Controllers\Controller;
use App\Services\CachePatternService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CacheController extends Controller
{
    public function __construct(
        protected CachePatternService $cache
    ) {}

    /**
     * Show the cache management UI: one card per group, each with
     * per-sub-module clear buttons.
     */
    public function index()
    {
        $structure = CacheKeyEnum::structure();
        $validation = CacheKeyEnum::validateStructure();

        return view('platform.admin.cache.index', compact('structure', 'validation'));
    }

    /**
     * Clear a single sub-module (or a whole group if $subModule is null).
     */
    public function clearSubModule(Request $request, string $group, ?string $subModule = null): RedirectResponse
    {
        if ($subModule === null) {
            $this->cache->clearForGroup($group);
            $message = "Cleared all caches in group [{$group}].";
        } else {
            $this->cache->clearForSubModule($group, $subModule);
            $message = "Cleared cache [{$group}/{$subModule}].";
        }

        return redirect()
            ->route('admin.cache.index')
            ->with('status', $message);
    }

    /**
     * Clear an entire group.
     */
    public function clearGroup(Request $request, string $group): RedirectResponse
    {
        $this->cache->clearForGroup($group);

        return redirect()
            ->route('admin.cache.index')
            ->with('status', "Cleared all caches in group [{$group}].");
    }

    /**
     * Clear every registered cache key/pattern.
     */
    public function clearAll(Request $request): RedirectResponse
    {
        $withOptimize = $request->boolean('optimize');
        $this->cache->clearAll($withOptimize);

        $message = 'Cleared all managed caches.';
        if ($withOptimize) {
            $message .= ' Ran optimize:clear.';
        }

        return redirect()
            ->route('admin.cache.index')
            ->with('status', $message);
    }

    /**
     * Self-check: validate that every enum case is registered in structure().
     */
    public function validateStructure()
    {
        $result = CacheKeyEnum::validateStructure();

        return redirect()
            ->route('admin.cache.index')
            ->with($result['passed'] ? 'status' : 'error', $result['message']);
    }
}
