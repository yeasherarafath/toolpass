@extends('layouts.app')

@section('title', $task->title)

@section('header')
    <h2 class="page-title">{{ $task->title }}</h2>
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <p>Type: <span class="badge bg-info">{{ $task->type }}</span> &middot; Priority: {{ $task->priority }} &middot; Status: {{ $task->status }}</p>
            <p>{{ $task->description }}</p>

            @if ($task->otpRequest)
                <p>OTP request #{{ $task->otpRequest->id }} &middot; {{ $task->otpRequest->status }}</p>
            @endif
            @if ($task->deviceResetRequest)
                <p>Device reset request #{{ $task->deviceResetRequest->id }} &middot; {{ $task->deviceResetRequest->status }}</p>
            @endif

            @if ($task->status !== 'completed' && $task->status !== 'cancelled')
                <form method="POST" action="{{ route('business.tasks.complete', $task) }}">
                    @csrf
                    <button class="btn btn-success">Complete</button>
                </form>
            @endif
        </div>
    </div>
@endsection
