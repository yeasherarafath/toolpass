@extends('layouts.app')

@section('title', 'Edit Owner')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="page-title">Edit Owner</h2>
        <a href="{{ route('admin.owners.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
    </div>
@endsection

@section('content')
    <form method="POST" action="{{ route('admin.owners.update', $owner) }}">
        @csrf @method('PUT')
        <div class="card">
            <div class="card-body">
                <div class="mb-3"><label class="form-label">Name</label><input type="text" name="name" class="form-control" value="{{ old('name', $owner->name) }}" required></div>
                <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $owner->email) }}" required>@error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="mb-3"><label class="form-label">Phone</label><input type="text" name="phone" class="form-control" value="{{ old('phone', $owner->phone) }}"></div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Leave blank to keep current">
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3"><label class="form-label">Business name</label><input type="text" name="business_name" class="form-control" value="{{ old('business_name', $owner->business_name) }}"></div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tenant</label>
                        <select name="tenant_id" class="form-select">
                            <option value="">No tenant</option>
                            @foreach ($tenants as $tenant)
                                <option value="{{ $tenant->id }}" {{ old('tenant_id', $owner->tenant_id) == $tenant->id ? 'selected' : '' }}>{{ $tenant->id }} ({{ $tenant->business_name }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            @foreach (['active','suspended','banned'] as $s)
                                <option value="{{ $s }}" {{ old('status', $owner->status) === $s ? 'selected' : '' }}>{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-footer text-end"><button type="submit" class="btn btn-primary">Save owner</button></div>
        </div>
    </form>
@endsection
