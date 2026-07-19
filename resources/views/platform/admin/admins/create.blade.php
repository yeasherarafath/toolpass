@extends('layouts.god')

@section('title', 'New Admin')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="page-title">New Admin</h2>
        <a href="{{ route('admin.admins.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
    </div>
@endsection

@section('content')
    <form method="POST" action="{{ route('admin.admins.store') }}">
        @csrf
        <div class="card">
            <div class="card-body">
                <div class="mb-3"><label class="form-label">Name</label><input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>@error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="mb-3"><label class="form-label">Phone</label><input type="text" name="phone" class="form-control" value="{{ old('phone') }}"></div>
                <div class="mb-3"><label class="form-label">Password</label><input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>@error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="active">active</option>
                        <option value="suspended">suspended</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Roles</label>
                    <select name="roles[]" class="form-select" multiple>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}" {{ in_array($role->id, old('roles', [])) ? 'selected' : '' }}>{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="card-footer text-end"><button type="submit" class="btn btn-primary">Save admin</button></div>
        </div>
    </form>
@endsection
