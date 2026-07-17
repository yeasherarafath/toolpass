@extends('layouts.app')

@section('title', 'Business Settings')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="page-title">Business Settings</h2>
        <a href="{{ route('owner.dashboard') }}" class="btn btn-outline-secondary btn-sm">Back to dashboard</a>
    </div>
@endsection

@section('content')
    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('owner.settings.update') }}">
        @csrf
        @method('PUT')

        <div class="card">
            <div class="card-header"><h3 class="card-title">Profile</h3></div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $owner->name) }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $owner->phone) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Business name</label>
                    <input type="text" name="business_name" class="form-control @error('business_name') is-invalid @enderror" value="{{ old('business_name', $owner->business_name) }}" required>
                    @error('business_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header"><h3 class="card-title">Change password</h3></div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Current password</label>
                    <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror">
                    @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">New password</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Confirm new password</label>
                    <input type="password" name="password_confirmation" class="form-control">
                </div>
            </div>
            <div class="card-footer text-end">
                <button type="submit" class="btn btn-primary">Save profile</button>
            </div>
        </div>
    </form>
@endsection
