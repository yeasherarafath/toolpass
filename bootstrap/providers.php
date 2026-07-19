<?php

use App\Providers\AppServiceProvider;
use App\Providers\Platform\SuperAdminPermissionProvider;
use App\Providers\TenancyServiceProvider;

return [
    AppServiceProvider::class,
    TenancyServiceProvider::class,
    SuperAdminPermissionProvider::class,
];
