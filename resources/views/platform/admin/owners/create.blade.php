@extends('layouts.app')

@section('title', 'New Owner')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="page-title">New Owner</h2>
        <a href="{{ route('admin.owners.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
    </div>
@endsection

@section('content')
    <form method="POST" action="{{ route('admin.owners.store') }}">
        @csrf
        <div class="card">
            <div class="card-body">
                <div class="mb-3"><label class="form-label">Name</label><input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>@error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="mb-3"><label class="form-label">Phone</label><input type="text" name="phone" class="form-control" value="{{ old('phone') }}"></div>
                <div class="mb-3"><label class="form-label">Password</label><input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>@error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="mb-3"><label class="form-label">Business name</label><input type="text" name="business_name" class="form-control" value="{{ old('business_name') }}"></div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tenant</label>
                        <select name="tenant_id" class="form-select">
                            <option value="">No tenant</option>
                            @foreach ($tenants as $tenant)
                                <option value="{{ $tenant->id }}" {{ old('tenant_id') == $tenant->id ? 'selected' : '' }}>{{ $tenant->id }} ({{ $tenant->business_name }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active">active</option>
                            <option value="suspended">suspended</option>
                            <option value="banned">banned</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-footer text-end"><button type="submit" class="btn btn-primary">Save owner</button></div>
        </div>
    </form>
@endsection
