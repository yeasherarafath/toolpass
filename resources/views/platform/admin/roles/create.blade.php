@extends('layouts.god')

@section('title', 'New Role')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="page-title">New Role</h2>
        <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
    </div>
@endsection

@section('content')
    <form method="POST" action="{{ route('admin.roles.store') }}">
        @csrf
        <div class="card">
            <div class="card-body">
                <div class="mb-3"><label class="form-label">Name</label><input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="mb-3">
                    <label class="form-label">Permissions</label>
                    <select name="permissions[]" class="form-select" multiple size="10">
                        @foreach ($permissions as $permission)
                            <option value="{{ $permission->id }}" {{ in_array($permission->id, old('permissions', [])) ? 'selected' : '' }}>{{ $permission->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="card-footer text-end"><button type="submit" class="btn btn-primary">Save role</button></div>
        </div>
    </form>
@endsection
