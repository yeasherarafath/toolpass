@extends('layouts.god')

@section('title', 'Edit Tenant')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="page-title">Edit Tenant</h2>
        <a href="{{ route('admin.tenants.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
    </div>
@endsection

@section('content')
    <form method="POST" action="{{ route('admin.tenants.update', $tenant) }}">
        @csrf @method('PUT')
        <div class="card">
            <div class="card-body">
                <div class="mb-3"><label class="form-label">Business name</label><input type="text" name="business_name" class="form-control" value="{{ old('business_name', $tenant->business_name) }}" required></div>
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        @foreach (['active','suspended','banned'] as $s)
                            <option value="{{ $s }}" {{ old('status', $tenant->status) === $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
                <hr>
                <h4 class="mb-3">Impersonate</h4>
                <form method="GET" action="{{ route('admin.tenants.impersonate', $tenant) }}">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-8">
                            <label class="form-label">Tenant user</label>
                            <select name="user_id" class="form-select">
                                @foreach ($users ?? [] as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->role }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100">Impersonate</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-footer text-end"><button type="submit" class="btn btn-primary">Save tenant</button></div>
        </div>
    </form>
@endsection
