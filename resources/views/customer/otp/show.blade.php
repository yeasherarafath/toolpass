@extends('layouts.customer')

@section('title', 'OTP Request')

@section('header')
    <h2 class="page-title">OTP for {{ $otpRequest->tool?->name }}</h2>
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <p>Status: <span class="badge bg-info">{{ $otpRequest->status }}</span></p>
            @if ($code)
                <div class="alert alert-success">
                    Your OTP code is: <strong>{{ $code }}</strong><br>
                    <small>Expires at {{ $otpRequest->otp_expires_at }}</small>
                </div>
            @else
                <p class="text-secondary">The code is not available yet or has expired.</p>
            @endif
        </div>
    </div>
@endsection
