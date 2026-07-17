@extends('layouts.app')

@section('header')
    <h2 class="page-title">New Tool Account</h2>
@endsection

@section('content')
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('business.tool-accounts.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Tool</label>
                        <select name="tool_id" class="form-select @error('tool_id') is-invalid @enderror" required>
                            <option value="">Select…</option>
                            @foreach ($tools as $tool)
                                <option value="{{ $tool->id }}" {{ old('tool_id') == $tool->id ? 'selected' : '' }}>{{ $tool->name }}</option>
                            @endforeach
                        </select>
                        @error('tool_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="row">
                        <div class="mb-3 col-sm-6">
                            <label class="form-label">Login email</label>
                            <input type="email" name="login_email" value="{{ old('login_email') }}" class="form-control">
                        </div>
                        <div class="mb-3 col-sm-6">
                            <label class="form-label">Login password</label>
                            <input type="text" name="login_password" class="form-control" autocomplete="off">
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3 col-sm-6">
                            <label class="form-label">Recovery email</label>
                            <input type="email" name="recovery_email" value="{{ old('recovery_email') }}" class="form-control">
                        </div>
                        <div class="mb-3 col-sm-6">
                            <label class="form-label">Account URL</label>
                            <input type="url" name="account_url" value="{{ old('account_url') }}" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3 col-sm-6">
                            <label class="form-label">Subscription type</label>
                            <input type="text" name="subscription_type" value="{{ old('subscription_type') }}" class="form-control">
                        </div>
                        <div class="mb-3 col-sm-6">
                            <label class="form-label">Max users</label>
                            <input type="number" name="max_users" value="{{ old('max_users') }}" class="form-control">
                        </div>
                    </div>
                    <div class="form-check form-switch mb-2">
                        <input type="checkbox" name="otp_required" value="1" class="form-check-input" {{ old('otp_required') ? 'checked' : '' }}>
                        <span class="form-check-label">OTP required</span>
                    </div>
                    <div class="row">
                        <div class="mb-3 col-sm-6">
                            <label class="form-label">OTP type</label>
                            <select name="otp_type" class="form-select">
                                @foreach (['email','sms','authenticator','none'] as $t)
                                    <option value="{{ $t }}" {{ old('otp_type', 'none') === $t ? 'selected' : '' }}>{{ $t }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3 col-sm-6">
                            <label class="form-label">OTP receiver</label>
                            <input type="text" name="otp_receiver" value="{{ old('otp_receiver') }}" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Two-factor secret</label>
                        <input type="text" name="two_factor_secret" class="form-control" autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Backup codes</label>
                        <textarea name="backup_codes" class="form-control"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            @foreach (['active','inactive','expired','issue'] as $s)
                                <option value="{{ $s }}" {{ old('status', 'active') === $s ? 'selected' : '' }}>{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control">{{ old('notes') }}</textarea>
                    </div>
                    <button class="btn btn-primary">Save</button>
                    <a href="{{ route('business.tool-accounts.index') }}" class="btn btn-ghost-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
@endsection
