@extends('layouts.guest')

@section('content')
    <div class="text-center mb-4">
        <h1 class="h2 fw-bold mb-1">{{ config('app.name', 'ToolPass') }} Platform</h1>
        <p class="text-secondary">Create your business account</p>
    </div>

    <form method="POST" action="{{ route('platform.register.attempt') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label">Your name</label>
            <input type="text" name="name" value="{{ old('name') }}"
                   class="form-control @error('name') is-invalid @enderror" required>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label class="form-label">Email address</label>
            <input type="email" name="email" value="{{ old('email') }}"
                   class="form-control @error('email') is-invalid @enderror" required>
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label class="form-label">Business name</label>
            <input type="text" name="business_name" value="{{ old('business_name') }}"
                   class="form-control @error('business_name') is-invalid @enderror" required>
            @error('business_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label class="form-label">Subdomain</label>
            <div class="input-group">
                <input type="text" name="slug" value="{{ old('slug') }}"
                       class="form-control @error('slug') is-invalid @enderror" placeholder="mybusiness" required>
                <span class="input-group-text">.{{ config('tenancy.central_domains.2', 'toolpass.test') }}</span>
                @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
        Already have an account? <a href="{{ route('platform.login') }}">Sign in</a>
    </div>
@endsection
