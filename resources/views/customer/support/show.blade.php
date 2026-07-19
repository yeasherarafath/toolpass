@extends('layouts.customer')

@section('title', 'Ticket ' . $ticket->ticket_number)

@section('header')
    <h2 class="page-title">{{ $ticket->subject }}</h2>
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            @foreach ($ticket->messages as $message)
                <div class="mb-2 border p-2 rounded">
                    <div class="text-secondary small">{{ $message->sender?->name }} &middot; {{ $message->is_staff_reply ? 'Staff' : 'You' }}</div>
                    <div>{{ $message->message }}</div>
                </div>
            @endforeach

            @if ($ticket->status !== 'closed')
                <form method="POST" action="{{ route('customer.support.reply', $ticket) }}">
                    @csrf
                    <textarea name="message" class="form-control mb-2" placeholder="Reply" required></textarea>
                    <button class="btn btn-primary">Reply</button>
                </form>
            @endif
        </div>
    </div>
@endsection
