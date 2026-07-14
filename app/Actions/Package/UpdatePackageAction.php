<?php

namespace App\Actions\Package;

use App\Models\Package;
use App\Models\PackageTool;
use App\Actions\Concerns\GeneratesUniqueSlug;

class UpdatePackageAction
{
    use GeneratesUniqueSlug;

    public function __invoke(Package $package, array $data): Package
    {
        $data['slug'] = $this->uniqueSlug(Package::class, $data['slug'] ?? '', $data['name'], $package->id);

        $tools = $data['tools'] ?? [];
        unset($data['tools']);

        $package->update($data);
        $this->syncTools($package, $tools);

        return $package;
    }

    protected function syncTools(Package $package, array $tools): void
    {
        $package->packageTools()->delete();

        foreach ($tools as $toolId) {
            $package->packageTools()->create(['tool_id' => $toolId]);
        }
    }
}
