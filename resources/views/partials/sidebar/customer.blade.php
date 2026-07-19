@php($items = [
    ['route' => 'customer.dashboard', 'label' => 'My Dashboard', 'icon' => 'ti ti-dashboard'],
    ['route' => 'customer.orders.index', 'label' => 'My Orders', 'icon' => 'ti ti-shopping-cart'],
    ['route' => 'customer.support.index', 'label' => 'Support', 'icon' => 'ti ti-help'],
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
