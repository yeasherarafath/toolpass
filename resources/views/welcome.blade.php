@extends('layouts.app')

@section('header')
    <h2 class="page-title">Tool Subscription Access Management</h2>
@endsection

@section('content')
    <div class="row row-deck row-cards">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <h1 class="display-5 fw-bold">ToolPass</h1>
                    <p class="text-secondary mb-4">
                        Sell access to premium digital tools. Customers buy single tools or bundles;
                        admins manage delivery, OTP, devices, and renewals from one dashboard.
                    </p>
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn btn-primary">Go to dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary">Sign in</a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
@endsection
