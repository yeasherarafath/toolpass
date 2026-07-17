@extends('layouts.app')

@section('title', 'Admin Tasks')

@section('header')
    <h2 class="page-title">Admin Tasks</h2>
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="table-responsive">
            <table class="table card-table">
                <thead><tr><th>Task</th><th>Type</th><th>Priority</th><th>Status</th><th></th></tr></thead>
                <tbody>
                    @forelse ($tasks as $task)
                        <tr>
                            <td>{{ $task->title }}</td>
                            <td><span class="badge bg-info">{{ $task->type }}</span></td>
                            <td>{{ $task->priority }}</td>
                            <td><span class="badge bg-secondary">{{ $task->status }}</span></td>
                            <td><a href="{{ route('business.tasks.show', $task) }}" class="btn btn-sm btn-primary">View</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-secondary">No open tasks.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $tasks->links() }}
    </div>
@endsection
