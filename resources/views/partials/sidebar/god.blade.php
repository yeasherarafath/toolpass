@php($items = [
    ['route' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => 'ti ti-dashboard'],
    ['route' => 'admin.admins.index', 'label' => 'Admins', 'icon' => 'ti ti-users'],
    ['route' => 'admin.roles.index', 'label' => 'Roles & Permissions', 'icon' => 'ti ti-shield'],
    ['route' => 'admin.permissions.index', 'label' => 'Permissions', 'icon' => 'ti ti-key'],
    ['route' => 'admin.owners.index', 'label' => 'Owners', 'icon' => 'ti ti-building-skyscraper'],
    ['route' => 'admin.plans.index', 'label' => 'Plans', 'icon' => 'ti ti-package'],
    ['route' => 'admin.subscriptions.index', 'label' => 'Subscriptions', 'icon' => 'ti ti-credit-card'],
    ['route' => 'admin.tenants.index', 'label' => 'Tenants', 'icon' => 'ti ti-building'],
    ['route' => 'admin.subscribe.create', 'label' => 'Subscribe Owner', 'icon' => 'ti ti-user-plus'],
    ['route' => 'admin.settings.edit', 'label' => 'Platform Settings', 'icon' => 'ti ti-settings'],
    ['route' => 'admin.cache.index', 'label' => 'Cache Management', 'icon' => 'ti ti-database'],
])

@foreach ($items as $item)
    <li class="nav-item {{ request()->routeIs($item['route']) ? 'active' : '' }}">
        <a class="nav-link" href="{{ route($item['route']) }}">
            <span class="nav-link-icon d-md-none d-lg-inline-block">
                <i class="{{ $item['icon'] }}"></i>
            </span>
            <span class="nav-link-title">{{ $item['label'] }}</span>
        </a>
    </li>
@endforeach
