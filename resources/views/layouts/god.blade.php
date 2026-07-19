@extends('layouts.app')

@section('navbar')
    <header class="navbar navbar-expand-md navbar-dark d-print-none" data-bs-theme="dark">
        <div class="container-xl">
            <div class="navbar-nav flex-row order-md-last">
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link d-flex lh-1 text-reset p-0 px-2" data-bs-toggle="dropdown" aria-label="Open user menu">
                        <span class="avatar avatar-sm bg-primary-lt">{{ initials(auth('admin')->user()->name ?? 'A') }}</span>
                        <div class="d-none d-xl-block ps-2">
                            <div>{{ auth('admin')->user()->name ?? 'Admin' }}</div>
                            <div class="mt-1 small text-secondary">{{ auth('admin')->user()->email ?? '' }}</div>
                        </div>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button class="dropdown-item">Logout</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>
@endsection

@section('sidebar')
    @include('partials.sidebar.god')
@endsection
