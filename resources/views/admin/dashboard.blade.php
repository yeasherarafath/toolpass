@extends('layouts.app')

@section('header')
    <h2 class="page-title">Admin Dashboard</h2>
@endsection

@section('content')
    <div class="row row-cards">
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="text-secondary">Pending payments</div>
                    <div class="h1">{{ $widgets['pending_payments'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="text-secondary">Ready for delivery</div>
                    <div class="h1">{{ $widgets['ready_for_delivery'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="text-secondary">Pending OTP</div>
                    <div class="h1">{{ $widgets['pending_otp'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="text-secondary">Device approvals</div>
                    <div class="h1">{{ $widgets['device_approvals'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="text-secondary">Expiring accesses</div>
                    <div class="h1">{{ $widgets['expiring_accesses'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="text-secondary">Full accounts</div>
                    <div class="h1">{{ $widgets['full_accounts'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="text-secondary">Open tickets</div>
                    <div class="h1">{{ $widgets['open_tickets'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="text-secondary">Revenue (paid)</div>
                    <div class="h1">{{ number_format($widgets['revenue'], 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('admin.orders.index') }}" class="btn btn-primary">Manage orders</a>
        <a href="{{ route('admin.tasks.index') }}" class="btn btn-secondary">Admin tasks</a>
        <a href="{{ route('admin.support.index') }}" class="btn btn-secondary">Support</a>
        <a href="{{ route('admin.offer-banners.index') }}" class="btn btn-secondary">Offer banners</a>
        <a href="{{ route('admin.settings.edit') }}" class="btn btn-secondary">Business settings</a>
    </div>
@endsection
