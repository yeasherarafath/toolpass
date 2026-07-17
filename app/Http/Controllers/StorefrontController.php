<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\OfferBanner;
use Illuminate\Http\Request;

class StorefrontController extends Controller
{
    public function index()
    {
        $packages = Package::where('status', 'active')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $banners = OfferBanner::active()->ordered()->get();

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
