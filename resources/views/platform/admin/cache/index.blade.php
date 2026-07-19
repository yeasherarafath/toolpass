@extends('layouts.god')

@section('title', 'Cache Management')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="page-title">Cache Management</h2>
        <div class="btn-list">
            <form method="POST" action="{{ route('admin.cache.validate') }}" class="d-inline">
                @csrf
                <button class="btn btn-outline-info btn-sm">Validate keys</button>
            </form>
            <form method="POST" action="{{ route('admin.cache.clear.all') }}" class="d-inline"
                  onsubmit="return confirm('Clear ALL managed caches?');">
                @csrf
                <button class="btn btn-outline-danger btn-sm">Clear all</button>
            </form>
        </div>
    </div>
@endsection

@section('content')
    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="alert alert-secondary">
        Clear cache by group or individual sub-module instead of flushing the entire
        cache store. Dynamic keys (per-tenant, per-user) are matched by pattern.
    </div>

    <div class="row row-cards">
        @foreach ($structure as $group => $groupData)
            <div class="col-md-6 col-xl-4">
                <div class="card">
                    <div class="card-header">
                        <span class="me-2 {{ $groupData['icon'] }}"></span>
                        <h3 class="card-title mb-0">{{ $groupData['label'] }}</h3>
                    </div>
                    <div class="card-body">
                        <p class="text-secondary small">{{ $groupData['description'] }}</p>

                        <div class="list-group list-group-transparent mb-3">
                            @foreach ($groupData['subModules'] as $slug => $module)
                                <div class="list-group-item px-0">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <div class="fw-bold">{{ $module['label'] }}</div>
                                            <div class="text-secondary small">{{ $module['description'] }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <form method="POST"
                                                  action="{{ route('admin.cache.clear.sub', [$group, $slug]) }}">
                                                @csrf
                                                <button class="btn btn-outline-secondary btn-sm">Clear</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <form method="POST" action="{{ route('admin.cache.clear.group', $group) }}">
                            @csrf
                            <button class="btn btn-outline-primary btn-sm w-100">Clear entire {{ $groupData['label'] }} group</button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-3">
        <span class="badge bg-{{ $validation['passed'] ? 'success' : 'danger' }}">
            {{ $validation['passed'] ? 'Cache key registry valid' : 'Registry issue: '.$validation['message'] }}
        </span>
    </div>
@endsection
