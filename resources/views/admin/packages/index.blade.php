@extends('layouts.app')

@section('header')
    <h2 class="page-title">Packages</h2>
@endsection

@section('content')
    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="row row-cards">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All packages</h3>
                    <div class="card-actions">
                        <a href="{{ route('business.packages.create') }}" class="btn btn-primary"><i class="ti ti-plus"></i> Add</a>
                    </div>
                </div>
                <div class="card-body border-0 p-0">
                    <div class="table-responsive">
                        <table class="table card-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Price</th>
                                    <th>Duration</th>
                                    <th>Status</th>
                                    <th class="w-1">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($packages as $package)
                                    <tr>
                                        <td>
                                            {{ $package->name }}
                                            @if ($package->is_featured)<span class="badge bg-green-lt">featured</span>@endif
                                            @if ($package->is_trial)<span class="badge bg-blue-lt">trial</span>@endif
                                        </td>
                                        <td><span class="badge bg-azure-lt">{{ $package->type }}</span></td>
                                        <td>{{ $package->currency }} {{ number_format($package->price, 2) }}</td>
                                        <td>{{ $package->duration_days }} days</td>
                                        <td><span class="badge bg-{{ $package->status === 'active' ? 'green' : 'yellow' }}-lt">{{ $package->status }}</span></td>
                                        <td>
                                            <a href="{{ route('business.packages.edit', $package) }}" class="btn btn-sm btn-ghost-secondary">Edit</a>
                                            <form method="POST" action="{{ route('business.packages.destroy', $package) }}" class="d-inline" onsubmit="return confirm('Delete?');">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-ghost-danger">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="text-center text-secondary">No packages.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($packages->hasPages())
                    <div class="card-footer">{{ $packages->links() }}</div>
                @endif
            </div>
        </div>
    </div>
@endsection
