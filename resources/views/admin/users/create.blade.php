@extends('layouts.app')

@section('header')
    <h2 class="page-title">Add user</h2>
@endsection

@section('content')
    <div class="row row-cards">
        <div class="col-md-8 col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">New user</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.users.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Full name</label>
                            <input type="text" name="name" value="{{ old('name') }}"
                                   class="form-control @error('name') is-invalid @enderror" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                   class="form-control @error('email') is-invalid @enderror" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone (optional)</label>
                            <input type="text" name="phone" value="{{ old('phone') }}" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                                <option value="customer" {{ old('role') === 'customer' ? 'selected' : '' }}>Customer</option>
                                <option value="staff" {{ old('role') === 'staff' ? 'selected' : '' }}>Staff</option>
                                <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="suspended" {{ old('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                                <option value="banned" {{ old('status') === 'banned' ? 'selected' : '' }}>Banned</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password"
                                   class="form-control @error('password') is-invalid @enderror" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-footer">
                            <button type="submit" class="btn btn-primary">Create user</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
