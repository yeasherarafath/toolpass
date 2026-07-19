@extends('layouts.god')

@section('title', 'Edit Role')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="page-title">Edit Role</h2>
        <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
    </div>
@endsection

@section('content')
    <form method="POST" action="{{ route('admin.roles.update', $role) }}">
        @csrf @method('PUT')
        <div class="card">
            <div class="card-body">
                <div class="mb-3"><label class="form-label">Name</label><input type="text" name="name" class="form-control" value="{{ old('name', $role->name) }}" required></div>
                <div class="mb-3">
                    <label class="form-label">Permissions</label>
                    <select name="permissions[]" class="form-select" multiple size="10">
                        @foreach ($permissions as $permission)
                            <option value="{{ $permission->id }}" {{ ($role->hasPermissionTo($permission->name) || in_array($permission->id, old('permissions', []))) ? 'selected' : '' }}>{{ $permission->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="card-footer text-end"><button type="submit" class="btn btn-primary">Save role</button></div>
        </div>
    </form>
@endsection
