<?php

namespace App\Actions\Package;

use App\Models\Package;
use App\Models\PackageCustomField;

class AddPackageCustomFieldAction
{
    public function __invoke(Package $package, array $data): PackageCustomField
    {
        $data['options'] = ($data['options'] ?? null)
            ? array_values(array_filter(preg_split('/\r\n|\r|\n/', $data['options']), 'strlen'))
            : null;

        $data['package_id'] = $package->id;

        return PackageCustomField::create($data);
    }
}
