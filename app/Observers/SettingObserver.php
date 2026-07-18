<?php

namespace App\Observers;

use App\Enum\CacheKeyEnum;
use App\Models\Setting;
use App\Services\CachePatternService;
use Illuminate\Support\Facades\Cache;

class SettingObserver
{
    public function saved(Setting $setting): void
    {
        $this->flush();
    }

    public function deleted(Setting $setting): void
    {
        $this->flush();
    }

    protected function flush(): void
    {
        if (function_exists('tenant') && tenant()) {
            Cache::forget(CacheKeyEnum::tenantSettings((string) tenant()->getTenantKey()));

            return;
        }

        app(CachePatternService::class)->clearByPattern('tenant:*');
    }
}
