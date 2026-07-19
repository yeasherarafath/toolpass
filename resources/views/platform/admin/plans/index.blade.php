@extends('layouts.god')

@section('title', 'Plans')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="page-title">Plans</h2>
        <a href="{{ route('admin.plans.create') }}" class="btn btn-primary btn-sm">New plan</a>
    </div>
@endsection

@section('content')
    @if (session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif
    <div class="card">
        <div class="table-responsive">
            <table class="table table-vcenter">
                <thead>
                    <tr>
                        <th>Name</th><th>Slug</th><th>Price</th><th>Cycle</th><th>Status</th><th class="w-1">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($plans as $plan)
                        <tr>
                            <td>{{ $plan->name }}</td>
                            <td>{{ $plan->slug }}</td>
                            <td>{{ $plan->price }} {{ $plan->currency }}</td>
                            <td>{{ $plan->billing_cycle }}</td>
                            <td><span class="badge bg-{{ $plan->status === 'active' ? 'green' : 'red' }}-lt">{{ $plan->status }}</span></td>
                            <td>
                                <a href="{{ route('admin.plans.edit', $plan) }}" class="btn btn-sm btn-ghost-secondary">Edit</a>
                                <form method="POST" action="{{ route('admin.plans.destroy', $plan) }}" class="d-inline" onsubmit="return confirm('Delete?');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-ghost-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-secondary">No plans yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
