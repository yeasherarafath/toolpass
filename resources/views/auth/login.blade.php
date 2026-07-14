@extends('layouts.guest')

@section('content')
    <div class="text-center mb-4">
        <h1 class="h2 fw-bold mb-1">{{ config('app.name', 'ToolPass') }}</h1>
        <p class="text-secondary">Sign in to your account</p>
    </div>

    <form method="POST" action="#">
        @csrf
        <div class="mb-3">
            <label class="form-label">Email address</label>
            <input type="email" name="email" class="form-control" placeholder="you@example.com" autocomplete="off">
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" placeholder="Your password">
        </div>
        <div class="form-footer">
            <button type="submit" class="btn btn-primary w-100">Sign in</button>
        </div>
    </form>

    <div class="text-center text-secondary mt-3">
        Auth UI will be implemented in T1.1
    </div>
@endsection
