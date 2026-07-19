@extends('layouts.god')

@section('title', 'Subscribe Owner')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="page-title">Subscribe Owner</h2>
        <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
    </div>
@endsection

@section('content')
    <form method="POST" action="{{ route('admin.subscribe.store') }}">
        @csrf
        <div class="card">
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Existing owner (optional)</label>
                    <select name="owner_id" class="form-select">
                        <option value="">— Create new owner —</option>
                        @foreach ($owners as $owner)
                            <option value="{{ $owner->id }}" {{ old('owner_id') == $owner->id ? 'selected' : '' }}>{{ $owner->name }} ({{ $owner->email }})</option>
                        @endforeach
                    </select>
                    <div class="form-hint">Leave empty to provision a brand-new owner + tenant.</div>
                </div>

                <hr>

                <div class="mb-3"><label class="form-label">Name</label><input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}">@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">@error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="mb-3"><label class="form-label">Password</label><input type="password" name="password" class="form-control @error('password') is-invalid @enderror">@error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="mb-3"><label class="form-label">Business name</label><input type="text" name="business_name" class="form-control" value="{{ old('business_name') }}"></div>
                <div class="mb-3"><label class="form-label">Subdomain</label><input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug') }}" placeholder="mybusiness">@error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>

                <hr>

                <div class="mb-3">
                    <label class="form-label">Plan</label>
                    <select name="plan_id" class="form-select @error('plan_id') is-invalid @enderror">
                        @foreach ($plans as $plan)
                            <option value="{{ $plan->id }}" {{ old('plan_id') == $plan->id ? 'selected' : '' }}>{{ $plan->name }} ({{ $plan->price }} {{ $plan->currency }})</option>
                        @endforeach
                    </select>
                    @error('plan_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active">active</option>
                            <option value="past_due">past_due</option>
                            <option value="cancelled">cancelled</option>
                            <option value="expired">expired</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3"><label class="form-label">Amount</label><input type="number" step="0.01" name="amount" class="form-control" value="{{ old('amount') }}"></div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3"><label class="form-label">Starts at</label><input type="date" name="starts_at" class="form-control" value="{{ old('starts_at') }}"></div>
                    <div class="col-md-6 mb-3"><label class="form-label">Ends at</label><input type="date" name="ends_at" class="form-control" value="{{ old('ends_at') }}"></div>
                </div>
            </div>
            <div class="card-footer text-end"><button type="submit" class="btn btn-primary">Subscribe</button></div>
        </div>
    </form>
@endsection
