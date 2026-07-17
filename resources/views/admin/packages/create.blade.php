@extends('layouts.app')

@section('header')
    <h2 class="page-title">New Package</h2>
@endsection

@section('content')
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('business.packages.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="row">
                        <div class="mb-3 col-sm-6">
                            <label class="form-label">Slug</label>
                            <input type="text" name="slug" value="{{ old('slug') }}" class="form-control">
                        </div>
                        <div class="mb-3 col-sm-6">
                            <label class="form-label">Type</label>
                            <select name="type" class="form-select" required>
                                @foreach (['single','multi','bundle'] as $t)
                                    <option value="{{ $t }}" {{ old('type') === $t ? 'selected' : '' }}>{{ $t }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3 col-sm-6">
                            <label class="form-label">Delivery type</label>
                            <select name="delivery_type" class="form-select" required>
                                @foreach (['instant','manual'] as $t)
                                    <option value="{{ $t }}" {{ old('delivery_type', 'instant') === $t ? 'selected' : '' }}>{{ $t }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3 col-sm-6">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                @foreach (['active','inactive','draft'] as $s)
                                    <option value="{{ $s }}" {{ old('status', 'active') === $s ? 'selected' : '' }}>{{ $s }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control">{{ old('description') }}</textarea>
                    </div>
                    <div class="row">
                        <div class="mb-3 col-sm-4">
                            <label class="form-label">Price</label>
                            <input type="number" step="0.01" name="price" value="{{ old('price', 0) }}" class="form-control" required>
                        </div>
                        <div class="mb-3 col-sm-4">
                            <label class="form-label">Currency</label>
                            <input type="text" name="currency" value="{{ old('currency', 'BDT') }}" class="form-control" required>
                        </div>
                        <div class="mb-3 col-sm-4">
                            <label class="form-label">Duration (days)</label>
                            <input type="number" name="duration_days" value="{{ old('duration_days', 30) }}" class="form-control" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3 col-sm-4">
                            <label class="form-label">Sort order</label>
                            <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" class="form-control">
                        </div>
                        <div class="mb-3 col-sm-4">
                            <label class="form-label">Trial days</label>
                            <input type="number" name="trial_days" value="{{ old('trial_days') }}" class="form-control">
                        </div>
                    </div>
                    <div class="form-check form-switch mb-2">
                        <input type="checkbox" name="is_featured" value="1" class="form-check-input" {{ old('is_featured') ? 'checked' : '' }}>
                        <span class="form-check-label">Featured</span>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input type="checkbox" name="is_trial" value="1" class="form-check-input" {{ old('is_trial') ? 'checked' : '' }}>
                        <span class="form-check-label">Trial package</span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Meta title</label>
                        <input type="text" name="meta_title" value="{{ old('meta_title') }}" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Meta description</label>
                        <textarea name="meta_description" class="form-control">{{ old('meta_description') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tools</label>
                        <select name="tools[]" class="form-select" multiple size="6">
                            @foreach ($tools as $tool)
                                <option value="{{ $tool->id }}" {{ is_array(old('tools')) && in_array($tool->id, old('tools')) ? 'selected' : '' }}>{{ $tool->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button class="btn btn-primary">Save</button>
                    <a href="{{ route('business.packages.index') }}" class="btn btn-ghost-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
@endsection
