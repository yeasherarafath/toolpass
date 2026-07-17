@extends('layouts.app')

@section('title', 'Platform Admin')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="page-title">Platform Administration</h2>
        <form method="POST" action="{{ route('admin.logout') }}" class="d-inline">
            @csrf
            <button class="btn btn-outline-secondary btn-sm">Logout ({{ $admin->name }})</button>
        </form>
    </div>
@endsection

@section('content')
    <div class="row row-deck row-cards">
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm"><div class="card-body"><div class="text-secondary">Admins</div><div class="h1">{{ $widgets['admins'] }}</div></div></div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm"><div class="card-body"><div class="text-secondary">Owners</div><div class="h1">{{ $widgets['owners'] }}</div></div></div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm"><div class="card-body"><div class="text-secondary">Tenants</div><div class="h1">{{ $widgets['tenants'] }}</div></div></div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm"><div class="card-body"><div class="text-secondary">Active subscriptions</div><div class="h1">{{ $widgets['active_subscriptions'] }}</div></div></div>
        </div>
    </div>

    <div class="row row-deck row-cards mt-3">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Management</h3></div>
                <div class="card-body">
                    <div class="list-group list-group-transparent">
                        <a href="{{ route('admin.admins.index') }}" class="list-group-item list-group-item-action">Admins</a>
                        <a href="{{ route('admin.roles.index') }}" class="list-group-item list-group-item-action">Roles &amp; permissions</a>
                        <a href="{{ route('admin.owners.index') }}" class="list-group-item list-group-item-action">Owners</a>
                        <a href="{{ route('admin.plans.index') }}" class="list-group-item list-group-item-action">Plans</a>
                        <a href="{{ route('admin.subscriptions.index') }}" class="list-group-item list-group-item-action">Subscriptions</a>
                        <a href="{{ route('admin.tenants.index') }}" class="list-group-item list-group-item-action">Tenants</a>
                        <a href="{{ route('admin.subscribe.create') }}" class="list-group-item list-group-item-action">Subscribe owner</a>
                        <a href="{{ route('admin.settings.edit') }}" class="list-group-item list-group-item-action">Platform settings</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
