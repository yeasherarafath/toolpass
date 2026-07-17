@extends('layouts.app')

@section('title', 'Subscriptions')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="page-title">Subscriptions</h2>
        <a href="{{ route('admin.subscribe.create') }}" class="btn btn-primary btn-sm">Subscribe owner</a>
    </div>
@endsection

@section('content')
    @if (session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif
    <div class="card">
        <div class="table-responsive">
            <table class="table table-vcenter">
                <thead>
                    <tr><th>Owner</th><th>Tenant</th><th>Plan</th><th>Status</th><th>Ends</th><th class="w-1">Actions</th></tr>
                </thead>
                <tbody>
                    @forelse ($subscriptions as $sub)
                        <tr>
                            <td>{{ $sub->owner->name ?? '-' }}</td>
                            <td>{{ $sub->tenant_id ?? '-' }}</td>
                            <td>{{ $sub->plan->name ?? '-' }}</td>
                            <td><span class="badge bg-{{ $sub->status === 'active' ? 'green' : 'red' }}-lt">{{ $sub->status }}</span></td>
                            <td>{{ $sub->ends_at ? $sub->ends_at->format('Y-m-d') : '-' }}</td>
                            <td>
                                <a href="{{ route('admin.subscriptions.edit', $sub) }}" class="btn btn-sm btn-ghost-secondary">Edit</a>
                                <form method="POST" action="{{ route('admin.subscriptions.destroy', $sub) }}" class="d-inline" onsubmit="return confirm('Delete?');">
                                    @csrf @method('DELETE')<button class="btn btn-sm btn-ghost-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-secondary">No subscriptions yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $subscriptions->links() }}</div>
    </div>
@endsection
