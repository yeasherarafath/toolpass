@extends('layouts.app')

@section('title', 'Permissions')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="page-title">Permissions</h2>
        <a href="{{ route('admin.roles.index') }}" class="btn btn-ghost-secondary btn-sm">Roles</a>
    </div>
@endsection

@section('content')
    @if (session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif

    <div class="alert alert-info">
        Showing {{ $permissions->count() }} admin permissions grouped by prefix.
    </div>

    @foreach ($groups as $prefix => $items)
        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">{{ $prefix }}</h3>
                <div class="card-actions">{{ $items->count() }}</div>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter">
                    <thead>
                        <tr>
                            <th>Permission</th>
                            <th>Used by roles</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $permission)
                            <tr>
                                <td><code>{{ $permission->name }}</code></td>
                                <td>{{ $permission->roles->pluck('name')->join(', ') ?: '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
@endsection
