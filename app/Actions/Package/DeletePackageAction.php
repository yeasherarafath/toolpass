<?php

namespace App\Actions\Package;

use App\Models\Package;

class DeletePackageAction
{
    public function __invoke(Package $package): void
    {
        $package->delete();
    }
}
