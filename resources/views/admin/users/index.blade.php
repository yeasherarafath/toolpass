@extends('layouts.app')

@section('header')
    <h2 class="page-title">Users</h2>
@endsection

@section('content')
    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="row row-cards">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All users</h3>
                    <div class="card-actions">
                        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                            <i class="ti ti-plus"></i> Add user
                        </a>
                    </div>
                </div>
                <div class="card-body border-0 p-0">
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th class="w-1">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $user)
                                    <tr>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            <span class="badge bg-{{ $user->role === 'admin' ? 'red' : ($user->role === 'staff' ? 'blue' : 'green') }}-lt">
                                                {{ ucfirst($user->role) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $user->status === 'active' ? 'green' : 'yellow' }}-lt">
                                                {{ $user->status }}
                                            </span>
                                        </td>
                                        <td>
                                            <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-ghost-secondary">
                                                    {{ $user->status === 'active' ? 'Suspend' : 'Activate' }}
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-secondary">No users found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($users->hasPages())
                    <div class="card-footer">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
