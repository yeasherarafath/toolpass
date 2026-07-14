@extends('layouts.app')

@section('header')
    <h2 class="page-title">Edit Tool</h2>
@endsection

@section('content')
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.tools.update', $tool) }}">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select name="category_id" class="form-select" required>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $tool->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" value="{{ old('name', $tool->name) }}" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Slug</label>
                        <input type="text" name="slug" value="{{ old('slug', $tool->slug) }}" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Website URL</label>
                        <input type="url" name="website_url" value="{{ old('website_url', $tool->website_url) }}" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Logo</label>
                        <input type="text" name="logo" value="{{ old('logo', $tool->logo) }}" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control">{{ old('description', $tool->description) }}</textarea>
                    </div>
                    <div class="row">
                        <div class="mb-3 col-sm-6">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="active" {{ old('status', $tool->status) === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $tool->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="mb-3 col-sm-6">
                            <label class="form-label">Access type</label>
                            <select name="access_type" class="form-select" required>
                                @foreach (['shared','credential','manual','none'] as $t)
                                    <option value="{{ $t }}" {{ old('access_type', $tool->access_type) === $t ? 'selected' : '' }}>{{ $t }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-check form-switch mb-2">
                        <input type="checkbox" name="otp_required" value="1" class="form-check-input" {{ old('otp_required', $tool->otp_required) ? 'checked' : '' }}>
                        <span class="form-check-label">OTP required</span>
                    </div>
                    <div class="row">
                        <div class="mb-3 col-sm-6">
                            <label class="form-label">OTP type</label>
                            <select name="otp_type" class="form-select">
                                @foreach (['email','sms','authenticator','none'] as $t)
                                    <option value="{{ $t }}" {{ old('otp_type', $tool->otp_type ?? 'none') === $t ? 'selected' : '' }}>{{ $t }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3 col-sm-6">
                            <label class="form-label">OTP note</label>
                            <input type="text" name="otp_note" value="{{ old('otp_note', $tool->otp_note) }}" class="form-control">
                        </div>
                    </div>
                    <div class="form-check form-switch mb-2">
                        <input type="checkbox" name="device_restriction_enabled" value="1" class="form-check-input" {{ old('device_restriction_enabled', $tool->device_restriction_enabled) ? 'checked' : '' }}>
                        <span class="form-check-label">Device restriction enabled</span>
                    </div>
                    <div class="row">
                        <div class="mb-3 col-sm-6">
                            <label class="form-label">Device limit type</label>
                            <select name="device_limit_type" class="form-select">
                                @foreach (['none','per_account','per_user'] as $t)
                                    <option value="{{ $t }}" {{ old('device_limit_type', $tool->device_limit_type ?? 'none') === $t ? 'selected' : '' }}>{{ $t }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3 col-sm-6">
                            <label class="form-label">Default max devices</label>
                            <input type="number" name="default_max_devices" value="{{ old('default_max_devices', $tool->default_max_devices) }}" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Device policy note</label>
                        <textarea name="device_policy_note" class="form-control">{{ old('device_policy_note', $tool->device_policy_note) }}</textarea>
                    </div>
                    <button class="btn btn-primary">Update</button>
                    <a href="{{ route('admin.tools.index') }}" class="btn btn-ghost-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
@endsection
