@php($items = [
    ['route' => 'owner.dashboard', 'label' => 'Dashboard', 'icon' => 'ti ti-dashboard'],
    ['route' => 'owner.settings.edit', 'label' => 'Business Settings', 'icon' => 'ti ti-settings'],
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
