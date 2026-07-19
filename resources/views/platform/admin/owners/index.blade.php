@extends('layouts.god')

@section('title', 'Owners')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="page-title">Owners</h2>
        <a href="{{ route('admin.owners.create') }}" class="btn btn-primary btn-sm">New owner</a>
    </div>
@endsection

@section('content')
    @if (session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif
    <div class="card">
        <div class="table-responsive">
            <table class="table table-vcenter">
                <thead>
                    <tr><th>Name</th><th>Email</th><th>Business</th><th>Tenant</th><th>Status</th><th class="w-1">Actions</th></tr>
                </thead>
                <tbody>
                    @forelse ($owners as $owner)
                        <tr>
                            <td>{{ $owner->name }}</td>
                            <td>{{ $owner->email }}</td>
                            <td>{{ $owner->business_name }}</td>
                            <td>{{ $owner->tenant->id ?? '-' }}</td>
                            <td><span class="badge bg-{{ $owner->status === 'active' ? 'green' : 'red' }}-lt">{{ $owner->status }}</span></td>
                            <td>
                                <a href="{{ route('admin.owners.edit', $owner) }}" class="btn btn-sm btn-ghost-secondary">Edit</a>
                                <form method="POST" action="{{ route('admin.owners.destroy', $owner) }}" class="d-inline" onsubmit="return confirm('Delete?');">
                                    @csrf @method('DELETE')<button class="btn btn-sm btn-ghost-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-secondary">No owners yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $owners->links() }}</div>
    </div>
@endsection
