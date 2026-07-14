<?php

namespace App\Actions\Package;

use App\Models\Package;
use App\Models\PackageCustomField;
use RuntimeException;

class RemovePackageCustomFieldAction
{
    public function __invoke(Package $package, PackageCustomField $field): void
    {
        if ($field->package_id !== $package->id) {
            throw new RuntimeException('Custom field does not belong to this package.');
        }

        $field->delete();
    }
}
