@extends('layouts.app')

@section('header')
    <h2 class="page-title">Tool Accounts</h2>
@endsection

@section('content')
    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="row row-cards">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All tool accounts</h3>
                    <div class="card-actions">
                        <a href="{{ route('admin.tool-accounts.create') }}" class="btn btn-primary"><i class="ti ti-plus"></i> Add</a>
                    </div>
                </div>
                <div class="card-body border-0 p-0">
                    <div class="table-responsive">
                        <table class="table card-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Tool</th>
                                    <th>Login email</th>
                                    <th>Max users</th>
                                    <th>Status</th>
                                    <th class="w-1">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($accounts as $account)
                                    <tr>
                                        <td>{{ $account->name }}</td>
                                        <td class="text-secondary">{{ $account->tool?->name }}</td>
                                        <td class="text-secondary">{{ $account->login_email }}</td>
                                        <td>{{ $account->max_users }}</td>
                                        <td><span class="badge bg-{{ $account->status === 'active' ? 'green' : 'yellow' }}-lt">{{ $account->status }}</span></td>
                                        <td>
                                            <a href="{{ route('admin.tool-accounts.edit', $account) }}" class="btn btn-sm btn-ghost-secondary">Edit</a>
                                            <form method="POST" action="{{ route('admin.tool-accounts.destroy', $account) }}" class="d-inline" onsubmit="return confirm('Delete?');">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-ghost-danger">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="text-center text-secondary">No tool accounts.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($accounts->hasPages())
                    <div class="card-footer">{{ $accounts->links() }}</div>
                @endif
            </div>
        </div>
    </div>
@endsection
