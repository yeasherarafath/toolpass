@extends('layouts.app')

@section('title', 'Admins')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="page-title">Platform Admins</h2>
        <a href="{{ route('admin.admins.create') }}" class="btn btn-primary btn-sm">New admin</a>
    </div>
@endsection

@section('content')
    @if (session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif
    <div class="card">
        <div class="table-responsive">
            <table class="table table-vcenter">
                <thead><tr><th>Name</th><th>Email</th><th>Roles</th><th>Status</th><th class="w-1">Actions</th></tr></thead>
                <tbody>
                    @forelse ($admins as $admin)
                        <tr>
                            <td>{{ $admin->name }}</td>
                            <td>{{ $admin->email }}</td>
                            <td>{{ $admin->roles->pluck('name')->join(', ') ?: '-' }}</td>
                            <td><span class="badge bg-{{ $admin->status === 'active' ? 'green' : 'red' }}-lt">{{ $admin->status }}</span></td>
                            <td>
                                <a href="{{ route('admin.admins.edit', $admin) }}" class="btn btn-sm btn-ghost-secondary">Edit</a>
                                <form method="POST" action="{{ route('admin.admins.destroy', $admin) }}" class="d-inline" onsubmit="return confirm('Delete?');">
                                    @csrf @method('DELETE')<button class="btn btn-sm btn-ghost-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-secondary">No admins yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $admins->links() }}</div>
    </div>
@endsection
