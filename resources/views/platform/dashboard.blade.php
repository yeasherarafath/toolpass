@extends('layouts.owner')

@section('title', 'Business Dashboard')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="page-title">Business Dashboard</h2>
        <div>
            @if ($tenant)
                <a href="https://{{ $tenant->domains->first()->domain ?? ($tenant->getKey() . '.' . config('tenancy.central_domains')[0]) }}" class="btn btn-outline-primary btn-sm" target="_blank">Open store</a>
            @endif
            <form method="POST" action="{{ route('owner.logout') }}" class="d-inline">
                @csrf
                <button class="btn btn-outline-secondary btn-sm">Logout ({{ $owner->name }})</button>
            </form>
        </div>
    </div>
@endsection

@section('content')
    <div class="row row-cards">
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="text-secondary">Active plans</div>
                    <div class="h1">{{ $widgets['plans'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="text-secondary">Your subscriptions</div>
                    <div class="h1">{{ $widgets['active_subscriptions'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header">
            <h3 class="card-title">Business profile</h3>
        </div>
        <div class="card-body">
            <p><strong>Name:</strong> {{ $owner->name }}</p>
            <p><strong>Business:</strong> {{ $owner->business_name }}</p>
            <p><strong>Email:</strong> {{ $owner->email }}</p>
            <p><strong>Status:</strong> {{ $owner->status }}</p>
            <a href="{{ route('owner.settings.edit') }}" class="btn btn-primary btn-sm">Manage profile &amp; settings</a>
        </div>
    </div>
@endsection
