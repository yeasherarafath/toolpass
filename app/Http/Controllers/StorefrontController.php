<?php

namespace App\Http\Controllers;

use App\Enum\CacheKeyEnum;
use App\Models\Package;
use App\Models\OfferBanner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class StorefrontController extends Controller
{
    public function index()
    {
        $tenantId = tenant()?->getTenantKey() ?? 'central';

        $packages = Cache::remember(
            CacheKeyEnum::STOREFRONT_PACKAGES_PREFIX->value.$tenantId,
            3600,
            function () {
                return Package::where('status', 'active')
                    ->orderBy('sort_order')
                    ->orderBy('name')
                    ->get();
            }
        );

        $banners = Cache::remember(
            CacheKeyEnum::STOREFRONT_BANNERS_PREFIX->value.$tenantId,
            3600,
            function () {
                return OfferBanner::active()->ordered()->get();
            }
        );

        return view('store.index', compact('packages', 'banners'));
    }

    public function show(Package $package)
    {
        if ($package->status !== 'active') {
            abort(404);
        }

        $package->load(['tools', 'packageCustomFields']);

        return view('store.show', compact('package'));
    }
}
