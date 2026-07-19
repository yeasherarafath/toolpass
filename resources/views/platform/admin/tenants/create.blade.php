@extends('layouts.god')

@section('title', 'New Tenant')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="page-title">New Tenant</h2>
        <a href="{{ route('admin.tenants.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
    </div>
@endsection

@section('content')
    <form method="POST" action="{{ route('admin.tenants.store') }}">
        @csrf
        <div class="card">
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Subdomain (ID)</label>
                    <div class="input-group">
                        <input type="text" name="id" class="form-control @error('id') is-invalid @enderror" value="{{ old('id') }}" required>
                        <span class="input-group-text">.{{ config('tenancy.central_domains')[0] }}</span>
                    </div>
                    @error('id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3"><label class="form-label">Business name</label><input type="text" name="business_name" class="form-control @error('business_name') is-invalid @enderror" value="{{ old('business_name') }}" required>@error('business_name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="active">active</option>
                        <option value="suspended">suspended</option>
                        <option value="banned">banned</option>
                    </select>
                </div>
            </div>
            <div class="card-footer text-end"><button type="submit" class="btn btn-primary">Create tenant</button></div>
        </div>
    </form>
@endsection
