@extends('layouts.god')

@section('title', 'Tenants')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="page-title">Tenants</h2>
        <a href="{{ route('admin.tenants.create') }}" class="btn btn-primary btn-sm">New tenant</a>
    </div>
@endsection

@section('content')
    @if (session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif
    <div class="card">
        <div class="table-responsive">
            <table class="table table-vcenter">
                <thead><tr><th>ID</th><th>Business</th><th>Status</th><th>Domain</th><th class="w-1">Actions</th></tr></thead>
                <tbody>
                    @forelse ($tenants as $tenant)
                        @php $domain = $tenant->domains->first()->domain ?? ($tenant->id . '.' . config('tenancy.central_domains')[0]); @endphp
                        <tr>
                            <td>{{ $tenant->id }}</td>
                            <td>{{ $tenant->business_name }}</td>
                            <td><span class="badge bg-{{ $tenant->status === 'active' ? 'green' : 'red' }}-lt">{{ $tenant->status }}</span></td>
                            <td><a href="https://{{ $domain }}" target="_blank">{{ $domain }}</a></td>
                            <td>
                                <a href="{{ route('admin.tenants.edit', $tenant) }}" class="btn btn-sm btn-ghost-secondary">Edit</a>
                                <form method="POST" action="{{ route('admin.tenants.destroy', $tenant) }}" class="d-inline" onsubmit="return confirm('Delete?');">
                                    @csrf @method('DELETE')<button class="btn btn-sm btn-ghost-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-secondary">No tenants yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $tenants->links() }}</div>
    </div>
@endsection
