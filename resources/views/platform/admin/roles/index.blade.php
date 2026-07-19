@extends('layouts.god')

@section('title', 'Roles')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="page-title">Roles &amp; Permissions</h2>
        <a href="{{ route('admin.roles.create') }}" class="btn btn-primary btn-sm">New role</a>
    </div>
@endsection

@section('content')
    @if (session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif
    <div class="card">
        <div class="table-responsive">
            <table class="table table-vcenter">
                <thead><tr><th>Name</th><th>Permissions</th><th class="w-1">Actions</th></tr></thead>
                <tbody>
                    @forelse ($roles as $role)
                        <tr>
                            <td>{{ $role->name }}</td>
                            <td>{{ $role->permissions->pluck('name')->join(', ') ?: '-' }}</td>
                            <td>
                                <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-sm btn-ghost-secondary">Edit</a>
                                <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" class="d-inline" onsubmit="return confirm('Delete?');">
                                    @csrf @method('DELETE')<button class="btn btn-sm btn-ghost-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center text-secondary">No roles yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
