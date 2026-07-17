@extends('layouts.app')

@section('header')
    <h2 class="page-title">Edit Package</h2>
@endsection

@section('content')
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('business.packages.update', $package) }}">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" value="{{ old('name', $package->name) }}" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="mb-3 col-sm-6">
                            <label class="form-label">Slug</label>
                            <input type="text" name="slug" value="{{ old('slug', $package->slug) }}" class="form-control">
                        </div>
                        <div class="mb-3 col-sm-6">
                            <label class="form-label">Type</label>
                            <select name="type" class="form-select" required>
                                @foreach (['single','multi','bundle'] as $t)
                                    <option value="{{ $t }}" {{ old('type', $package->type) === $t ? 'selected' : '' }}>{{ $t }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3 col-sm-6">
                            <label class="form-label">Delivery type</label>
                            <select name="delivery_type" class="form-select" required>
                                @foreach (['instant','manual'] as $t)
                                    <option value="{{ $t }}" {{ old('delivery_type', $package->delivery_type) === $t ? 'selected' : '' }}>{{ $t }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3 col-sm-6">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                @foreach (['active','inactive','draft'] as $s)
                                    <option value="{{ $s }}" {{ old('status', $package->status) === $s ? 'selected' : '' }}>{{ $s }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control">{{ old('description', $package->description) }}</textarea>
                    </div>
                    <div class="row">
                        <div class="mb-3 col-sm-4">
                            <label class="form-label">Price</label>
                            <input type="number" step="0.01" name="price" value="{{ old('price', $package->price) }}" class="form-control" required>
                        </div>
                        <div class="mb-3 col-sm-4">
                            <label class="form-label">Currency</label>
                            <input type="text" name="currency" value="{{ old('currency', $package->currency) }}" class="form-control" required>
                        </div>
                        <div class="mb-3 col-sm-4">
                            <label class="form-label">Duration (days)</label>
                            <input type="number" name="duration_days" value="{{ old('duration_days', $package->duration_days) }}" class="form-control" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3 col-sm-4">
                            <label class="form-label">Sort order</label>
                            <input type="number" name="sort_order" value="{{ old('sort_order', $package->sort_order) }}" class="form-control">
                        </div>
                        <div class="mb-3 col-sm-4">
                            <label class="form-label">Trial days</label>
                            <input type="number" name="trial_days" value="{{ old('trial_days', $package->trial_days) }}" class="form-control">
                        </div>
                    </div>
                    <div class="form-check form-switch mb-2">
                        <input type="checkbox" name="is_featured" value="1" class="form-check-input" {{ old('is_featured', $package->is_featured) ? 'checked' : '' }}>
                        <span class="form-check-label">Featured</span>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input type="checkbox" name="is_trial" value="1" class="form-check-input" {{ old('is_trial', $package->is_trial) ? 'checked' : '' }}>
                        <span class="form-check-label">Trial package</span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Meta title</label>
                        <input type="text" name="meta_title" value="{{ old('meta_title', $package->meta_title) }}" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Meta description</label>
                        <textarea name="meta_description" class="form-control">{{ old('meta_description', $package->meta_description) }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tools</label>
                        <select name="tools[]" class="form-select" multiple size="6">
                            @foreach ($tools as $tool)
                                <option value="{{ $tool->id }}" {{ (is_array(old('tools')) ? in_array($tool->id, old('tools')) : in_array($tool->id, $selectedTools)) ? 'selected' : '' }}>{{ $tool->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button class="btn btn-primary">Update</button>
                    <a href="{{ route('business.packages.index') }}" class="btn btn-ghost-secondary">Cancel</a>
                </form>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">Custom fields</h3>
            </div>
            <div class="card-body">
                @if ($package->packageCustomFields->count())
                    <div class="table-responsive mb-3">
                        <table class="table card-table">
                            <thead>
                                <tr><th>Label</th><th>Name</th><th>Type</th><th>Required</th><th>Status</th><th></th></tr>
                            </thead>
                            <tbody>
                                @foreach ($package->packageCustomFields as $field)
                                    <tr>
                                        <td>{{ $field->label }}</td>
                                        <td class="text-secondary">{{ $field->name }}</td>
                                        <td><span class="badge bg-azure-lt">{{ $field->type }}</span></td>
                                        <td>{{ $field->is_required ? 'Yes' : 'No' }}</td>
                                        <td><span class="badge bg-{{ $field->status === 'active' ? 'green' : 'yellow' }}-lt">{{ $field->status }}</span></td>
                                        <td>
                                            <form method="POST" action="{{ route('business.packages.custom-fields.destroy', [$package, $field]) }}" onsubmit="return confirm('Remove field?');">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-ghost-danger">Remove</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                <form method="POST" action="{{ route('business.packages.custom-fields.store', $package) }}">
                    @csrf
                    <div class="row g-2 align-items-end">
                        <div class="col">
                            <label class="form-label">Label</label>
                            <input type="text" name="label" class="form-control" required>
                        </div>
                        <div class="col">
                            <label class="form-label">Name (a-z0-9_)</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col">
                            <label class="form-label">Type</label>
                            <select name="type" class="form-select">
                                @foreach (['text','email','number','textarea','select','checkbox','file'] as $t)
                                    <option value="{{ $t }}">{{ $t }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-auto">
                            <label class="form-check form-switch mb-2">
                                <input type="checkbox" name="is_required" value="1" class="form-check-input">
                                <span class="form-check-label">Required</span>
                            </label>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-primary">Add field</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
