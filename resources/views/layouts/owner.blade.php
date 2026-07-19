@extends('layouts.app')

@section('navbar')
    <header class="navbar navbar-expand-md navbar-dark d-print-none" data-bs-theme="dark">
        <div class="container-xl">
            <div class="navbar-nav flex-row order-md-last">
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link d-flex lh-1 text-reset p-0 px-2" data-bs-toggle="dropdown" aria-label="Open user menu">
                        <span class="avatar avatar-sm bg-primary-lt">{{ initials(auth('owner')->user()->name ?? 'O') }}</span>
                        <div class="d-none d-xl-block ps-2">
                            <div>{{ auth('owner')->user()->name ?? 'Owner' }}</div>
                            <div class="mt-1 small text-secondary">{{ auth('owner')->user()->business_name ?? '' }}</div>
                        </div>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                        <form method="POST" action="{{ route('owner.logout') }}">
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
    @include('partials.sidebar.owner')
@endsection
