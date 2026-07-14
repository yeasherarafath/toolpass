@extends('layouts.app')

@section('header')
    <h2 class="page-title">My Dashboard</h2>
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($widgets['announcements']->isNotEmpty())
        @foreach ($widgets['announcements'] as $announcement)
            <div class="alert alert-{{ $announcement->type === 'warning' ? 'warning' : 'info' }}">
                <strong>{{ $announcement->title }}</strong><br>
                {{ $announcement->message }}
            </div>
        @endforeach
    @endif

    <h3>My Tools</h3>
    <div class="row row-cards">
        @forelse ($widgets['delivered_accesses'] as $access)
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">{{ $access->tool?->name }}</h4>
                        <span class="badge bg-success">delivered</span>
                    </div>
                    <div class="card-body">
                        <p>{{ $access->delivery_note ?? $access->access_note ?? 'Access is ready.' }}</p>
                        <p class="text-secondary">Expires: {{ $access->expires_at }}</p>
                        <form method="POST" action="{{ route('customer.otp.request') }}" class="d-inline">
                            @csrf
                            <input type="hidden" name="user_tool_access_id" value="{{ $access->id }}">
                            <button class="btn btn-sm btn-warning">Request OTP</button>
                        </form>
                        <form method="POST" action="{{ route('customer.devices.reset') }}" class="d-inline">
                            @csrf
                            <input type="hidden" name="user_tool_access_id" value="{{ $access->id }}">
                            <button class="btn btn-sm btn-outline-danger">Reset device</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="empty">
                    <p class="empty-title">No active tools yet</p>
                    <a href="{{ route('store.index') }}" class="btn btn-primary">Browse store</a>
                </div>
            </div>
        @endforelse
    </div>

    @if ($widgets['pending_accesses']->isNotEmpty())
        <h3>Pending</h3>
        <ul>
            @foreach ($widgets['pending_accesses'] as $access)
                <li>{{ $access->tool?->name }} — <span class="badge bg-warning">pending</span></li>
            @endforeach
        </ul>
    @endif

    <div class="mt-3">
        <a href="{{ route('customer.orders.index') }}" class="btn btn-primary">My orders</a>
        <a href="{{ route('customer.support.index') }}" class="btn btn-secondary">Support ({{ $widgets['open_tickets'] }})</a>
    </div>
@endsection
