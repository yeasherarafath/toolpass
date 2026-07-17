@extends('layouts.app')

@section('title', 'Platform Dashboard')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="page-title">Platform Dashboard</h2>
        <form method="POST" action="{{ route('platform.logout') }}">
            @csrf
            <button class="btn btn-outline-secondary btn-sm">Logout ({{ $owner->name }})</button>
        </form>
    </div>
@endsection

@section('content')
    <div class="row row-cards">
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="text-secondary">Tenants</div>
                    <div class="h1">{{ $widgets['tenants'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="text-secondary">Owners</div>
                    <div class="h1">{{ $widgets['owners'] }}</div>
                </div>
            </div>
        </div>
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
                    <div class="text-secondary">Active subscriptions</div>
                    <div class="h1">{{ $widgets['active_subscriptions'] }}</div>
                </div>
            </div>
        </div>
    </div>
@endsection
