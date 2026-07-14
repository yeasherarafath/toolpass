@extends('layouts.app')

@section('header')
    <h2 class="page-title">Tools</h2>
@endsection

@section('content')
    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="row row-cards">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All tools</h3>
                    <div class="card-actions">
                        <a href="{{ route('admin.tools.create') }}" class="btn btn-primary"><i class="ti ti-plus"></i> Add</a>
                    </div>
                </div>
                <div class="card-body border-0 p-0">
                    <div class="table-responsive">
                        <table class="table card-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Access</th>
                                    <th>OTP</th>
                                    <th>Status</th>
                                    <th class="w-1">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($tools as $tool)
                                    <tr>
                                        <td>{{ $tool->name }}</td>
                                        <td class="text-secondary">{{ $tool->category?->name }}</td>
                                        <td><span class="badge bg-blue-lt">{{ $tool->access_type }}</span></td>
                                        <td>{{ $tool->otp_required ? 'Yes' : 'No' }}</td>
                                        <td><span class="badge bg-{{ $tool->status === 'active' ? 'green' : 'yellow' }}-lt">{{ $tool->status }}</span></td>
                                        <td>
                                            <a href="{{ route('admin.tools.edit', $tool) }}" class="btn btn-sm btn-ghost-secondary">Edit</a>
                                            <form method="POST" action="{{ route('admin.tools.destroy', $tool) }}" class="d-inline" onsubmit="return confirm('Delete?');">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-ghost-danger">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="text-center text-secondary">No tools.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($tools->hasPages())
                    <div class="card-footer">{{ $tools->links() }}</div>
                @endif
            </div>
        </div>
    </div>
@endsection
