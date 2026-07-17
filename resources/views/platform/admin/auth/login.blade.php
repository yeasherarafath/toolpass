@extends('layouts.guest')

@section('content')
    <div class="text-center mb-4">
        <h1 class="h2 fw-bold mb-1">{{ config('app.name', 'ToolPass') }}</h1>
        <p class="text-secondary">Platform administrator sign in</p>
    </div>

    <form method="POST" action="{{ route('admin.login.attempt') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label">Email address</label>
            <input type="email" name="email" value="{{ old('email') }}"
                   class="form-control @error('email') is-invalid @enderror"
                   placeholder="admin@example.com" autocomplete="off" required>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" placeholder="Your password" required>
        </div>
        <div class="mb-3">
            <label class="form-check">
                <input type="checkbox" name="remember" class="form-check-input">
                <span class="form-check-label">Remember me</span>
            </label>
        </div>
        <div class="form-footer">
            <button type="submit" class="btn btn-primary w-100">Sign in</button>
        </div>
    </form>
@endsection
