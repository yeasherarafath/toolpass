@extends('layouts.god')

@section('title', 'Edit Plan')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="page-title">Edit Plan</h2>
        <a href="{{ route('admin.plans.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
    </div>
@endsection

@section('content')
    <form method="POST" action="{{ route('admin.plans.update', $plan) }}">
        @csrf @method('PUT')
        <div class="card">
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $plan->name) }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Slug</label>
                    <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug', $plan->slug) }}" required>
                    @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control">{{ old('description', $plan->description) }}</textarea>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Price</label>
                        <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price', $plan->price) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Currency</label>
                        <input type="text" name="currency" class="form-control" value="{{ old('currency', $plan->currency) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Billing cycle</label>
                        <select name="billing_cycle" class="form-select">
                            @foreach (['monthly','yearly','lifetime'] as $c)
                                <option value="{{ $c }}" {{ $plan->billing_cycle === $c ? 'selected' : '' }}>{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3"><label class="form-label">Max staff</label><input type="number" name="max_staff" class="form-control" value="{{ old('max_staff', $plan->max_staff) }}"></div>
                    <div class="col-md-4 mb-3"><label class="form-label">Max customers</label><input type="number" name="max_customers" class="form-control" value="{{ old('max_customers', $plan->max_customers) }}"></div>
                    <div class="col-md-4 mb-3"><label class="form-label">Max packages</label><input type="number" name="max_packages" class="form-control" value="{{ old('max_packages', $plan->max_packages) }}"></div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3"><label class="form-label">Email quota</label><input type="number" name="email_quota" class="form-control" value="{{ old('email_quota', $plan->email_quota) }}"></div>
                    <div class="col-md-6 mb-3"><label class="form-label">SMS quota</label><input type="number" name="sms_quota" class="form-control" value="{{ old('sms_quota', $plan->sms_quota) }}"></div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active" {{ $plan->status === 'active' ? 'selected' : '' }}>active</option>
                            <option value="inactive" {{ $plan->status === 'inactive' ? 'selected' : '' }}>inactive</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3"><label class="form-label">Sort order</label><input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $plan->sort_order) }}"></div>
                </div>
            </div>
            <div class="card-footer text-end"><button type="submit" class="btn btn-primary">Save plan</button></div>
        </div>
    </form>
@endsection
