@extends('layouts.guest')

@section('content')
    <div class="text-center">
        <h1 class="h1 fw-bold mb-2">{{ $branding['site_name'] ?? config('app.name', 'ToolPass') }}</h1>
        <p class="text-secondary mb-4">{{ $branding['site_description'] ?? 'Multi-tenant tool subscription platform.' }}</p>

        <div class="d-grid gap-2 col-6 mx-auto">
            <a href="{{ route('owner.login') }}" class="btn btn-primary btn-lg">Business sign in</a>
            <a href="{{ route('owner.register') }}" class="btn btn-outline-primary btn-lg">Create a business</a>
        </div>

        <div class="text-secondary mt-4 small">
            Platform administrators: <a href="{{ route('admin.login') }}">admin access</a>
        </div>
    </div>
@endsection
