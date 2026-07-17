@extends('layouts.app')

@section('title', 'Edit Subscription')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="page-title">Edit Subscription</h2>
        <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
    </div>
@endsection

@section('content')
    <form method="POST" action="{{ route('admin.subscriptions.update', $subscription) }}">
        @csrf @method('PUT')
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Owner</label>
                        <select name="owner_id" class="form-select">
                            @foreach ($owners as $owner)
                                <option value="{{ $owner->id }}" {{ old('owner_id', $subscription->owner_id) == $owner->id ? 'selected' : '' }}>{{ $owner->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Plan</label>
                        <select name="plan_id" class="form-select">
                            @foreach ($plans as $plan)
                                <option value="{{ $plan->id }}" {{ old('plan_id', $subscription->plan_id) == $plan->id ? 'selected' : '' }}>{{ $plan->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tenant</label>
                        <select name="tenant_id" class="form-select">
                            <option value="">No tenant</option>
                            @foreach ($tenants as $tenant)
                                <option value="{{ $tenant->id }}" {{ old('tenant_id', $subscription->tenant_id) == $tenant->id ? 'selected' : '' }}>{{ $tenant->id }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            @foreach (['active','past_due','cancelled','expired'] as $s)
                                <option value="{{ $s }}" {{ old('status', $subscription->status) === $s ? 'selected' : '' }}>{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3"><label class="form-label">Starts at</label><input type="date" name="starts_at" class="form-control" value="{{ old('starts_at', $subscription->starts_at?->format('Y-m-d')) }}"></div>
                    <div class="col-md-4 mb-3"><label class="form-label">Ends at</label><input type="date" name="ends_at" class="form-control" value="{{ old('ends_at', $subscription->ends_at?->format('Y-m-d')) }}"></div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3"><label class="form-label">Amount</label><input type="number" step="0.01" name="amount" class="form-control" value="{{ old('amount', $subscription->amount) }}"></div>
                    <div class="col-md-6 mb-3"><label class="form-label">Currency</label><input type="text" name="currency" class="form-control" value="{{ old('currency', $subscription->currency) }}"></div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-check">
                            <input type="hidden" name="quota_enforced" value="0">
                            <input type="checkbox" name="quota_enforced" value="1" class="form-check-input" {{ old('quota_enforced', $subscription->quota_enforced) ? 'checked' : '' }}>
                            <span class="form-check-label">Enforce quota</span>
                        </label>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-check">
                            <input type="hidden" name="is_suspended" value="0">
                            <input type="checkbox" name="is_suspended" value="1" class="form-check-input" {{ old('is_suspended', $subscription->is_suspended) ? 'checked' : '' }}>
                            <span class="form-check-label">Suspended</span>
                        </label>
                    </div>
                </div>
                <div class="mb-3"><label class="form-label">Suspend reason</label><input type="text" name="suspend_reason" class="form-control" value="{{ old('suspend_reason', $subscription->suspend_reason) }}"></div>
                <div class="mb-3"><label class="form-label">Note</label><textarea name="note" class="form-control">{{ old('note', $subscription->note) }}</textarea></div>
            </div>
            <div class="card-footer text-end"><button type="submit" class="btn btn-primary">Save subscription</button></div>
        </div>
    </form>
@endsection
