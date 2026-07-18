<?php

namespace App\Observers;

use App\Enum\CacheKeyEnum;
use App\Models\PlatformSetting;
use Illuminate\Support\Facades\Cache;

class PlatformSettingObserver
{
    public function saved(PlatformSetting $setting): void
    {
        Cache::forget(CacheKeyEnum::PLATFORM_SETTINGS->value);
    }

    public function deleted(PlatformSetting $setting): void
    {
        Cache::forget(CacheKeyEnum::PLATFORM_SETTINGS->value);
    }
}
