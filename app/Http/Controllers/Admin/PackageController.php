<?php

namespace App\Http\Controllers\Admin;

use App\Enum\CacheKeyEnum;
use App\Http\Controllers\Controller;
use App\Actions\Package\CreatePackageAction;
use App\Actions\Package\UpdatePackageAction;
use App\Actions\Package\DeletePackageAction;
use App\Models\Package;
use App\Models\Tool;
use App\Services\CachePatternService;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function __construct(protected CachePatternService $cache)
    {
    }

    public function index()
    {
        $packages = Package::orderBy('sort_order')->orderBy('name')->paginate(20);

        return view('admin.packages.index', compact('packages'));
    }

    public function create()
    {
        $tools = Tool::where('status', 'active')->orderBy('name')->get();

        return view('admin.packages.create', compact('tools'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'slug' => ['nullable', 'string', 'max:150', 'unique:packages,slug'],
            'type' => ['required', 'in:single,multi,bundle'],
            'delivery_type' => ['required', 'in:instant,manual'],
            'description' => ['nullable', 'string'],
            'meta_title' => ['nullable', 'string', 'max:200'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'price' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'max:3'],
            'duration_days' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'in:active,inactive,draft'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_featured' => ['boolean'],
            'is_trial' => ['boolean'],
            'trial_days' => ['nullable', 'integer', 'min:0'],
            'tools' => ['nullable', 'array'],
            'tools.*' => ['exists:tools,id'],
        ]);

        app(CreatePackageAction::class)($data);

        $this->flushStorefrontCaches();

        return redirect()->route('business.packages.index')->with('status', 'Package created.');
    }

    public function edit(Package $package)
    {
        $tools = Tool::where('status', 'active')->orderBy('name')->get();
        $selectedTools = $package->packageTools()->pluck('tool_id')->toArray();

        return view('admin.packages.edit', compact('package', 'tools', 'selectedTools'));
    }

    public function update(Request $request, Package $package)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'slug' => ['nullable', 'string', 'max:150', 'unique:packages,slug,' . $package->id],
            'type' => ['required', 'in:single,multi,bundle'],
            'delivery_type' => ['required', 'in:instant,manual'],
            'description' => ['nullable', 'string'],
            'meta_title' => ['nullable', 'string', 'max:200'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'price' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'max:3'],
            'duration_days' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'in:active,inactive,draft'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_featured' => ['boolean'],
            'is_trial' => ['boolean'],
            'trial_days' => ['nullable', 'integer', 'min:0'],
            'tools' => ['nullable', 'array'],
            'tools.*' => ['exists:tools,id'],
        ]);

        app(UpdatePackageAction::class)($package, $data);

        $this->flushStorefrontCaches();

        return redirect()->route('business.packages.index')->with('status', 'Package updated.');
    }

    public function destroy(Package $package)
    {
        app(DeletePackageAction::class)($package);

        $this->flushStorefrontCaches();

        return redirect()->route('business.packages.index')->with('status', 'Package deleted.');
    }

    protected function flushStorefrontCaches(): void
    {
        $this->cache->clearByPattern('storefront:packages:*');
        $this->cache->clearByPattern('storefront:banners:*');
    }
}
