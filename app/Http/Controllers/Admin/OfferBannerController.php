<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OfferBanner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OfferBannerController extends Controller
{
    public function index()
    {
        $banners = OfferBanner::ordered()->paginate(20);

        return view('admin.offer-banners.index', compact('banners'));
    }

    public function create()
    {
        return view('admin.offer-banners.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        unset($data['image']);

        if ($request->hasFile('image')) {
            $data['image_path'] = $this->storeImage($request->file('image'));
        }

        OfferBanner::create($data);

        return redirect()->route('admin.offer-banners.index')->with('status', 'Banner created.');
    }

    public function edit(OfferBanner $offerBanner)
    {
        return view('admin.offer-banners.edit', ['banner' => $offerBanner]);
    }

    public function update(Request $request, OfferBanner $offerBanner)
    {
        $data = $this->validateData($request);
        unset($data['image']);

        if ($request->hasFile('image')) {
            if ($offerBanner->image_path && Storage::disk('public')->exists($offerBanner->image_path)) {
                Storage::disk('public')->delete($offerBanner->image_path);
            }
            $data['image_path'] = $this->storeImage($request->file('image'));
        }

        $offerBanner->update($data);

        return redirect()->route('admin.offer-banners.index')->with('status', 'Banner updated.');
    }

    public function destroy(OfferBanner $offerBanner)
    {
        if ($offerBanner->image_path && Storage::disk('public')->exists($offerBanner->image_path)) {
            Storage::disk('public')->delete($offerBanner->image_path);
        }

        $offerBanner->delete();

        return redirect()->route('admin.offer-banners.index')->with('status', 'Banner deleted.');
    }

    protected function validateData(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:190'],
            'description' => ['nullable', 'string', 'max:1000'],
            'link' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'in:active,inactive'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);
    }

    protected function storeImage($file): string
    {
        $dir = 'tenants/' . (tenant() ? tenant()->getTenantKey() : 'shared') . '/banners';

        return $file->store($dir, 'public');
    }
}
