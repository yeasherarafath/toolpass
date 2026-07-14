@extends('layouts.guest')

@section('content')
    <div class="text-center mb-4">
        <h1 class="h2 fw-bold mb-1">Create account</h1>
        <p class="text-secondary">Register as a customer</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label">Full name</label>
            <input type="text" name="name" value="{{ old('name') }}"
                   class="form-control @error('name') is-invalid @enderror" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label class="form-label">Email address</label>
            <input type="email" name="email" value="{{ old('email') }}"
                   class="form-control @error('email') is-invalid @enderror" required>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label class="form-label">Phone (optional)</label>
            <input type="text" name="phone" value="{{ old('phone') }}" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password"
                   class="form-control @error('password') is-invalid @enderror" required>
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label class="form-label">Confirm password</label>
            <input type="password" name="password_confirmation" class="form-control" required>
        </div>
        <div class="form-footer">
            <button type="submit" class="btn btn-primary w-100">Create account</button>
        </div>
    </form>

    <div class="text-center text-secondary mt-3">
        <a href="{{ route('login') }}">Already have an account? Sign in</a>
    </div>
@endsection
