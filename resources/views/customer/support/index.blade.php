@extends('layouts.customer')

@section('title', 'Support')

@section('header')
    <h2 class="page-title">My Support Tickets</h2>
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('customer.support.store') }}" class="mb-3">
                @csrf
                <input type="text" name="subject" class="form-control mb-2" placeholder="Subject" required>
                <textarea name="message" class="form-control mb-2" placeholder="Describe your issue" required></textarea>
                <button class="btn btn-primary">Open ticket</button>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table card-table">
                <thead><tr><th>Ticket</th><th>Subject</th><th>Status</th><th></th></tr></thead>
                <tbody>
                    @forelse ($tickets as $ticket)
                        <tr>
                            <td>{{ $ticket->ticket_number }}</td>
                            <td>{{ $ticket->subject }}</td>
                            <td><span class="badge bg-secondary">{{ $ticket->status }}</span></td>
                            <td><a href="{{ route('customer.support.show', $ticket) }}" class="btn btn-sm btn-primary">View</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-secondary">No tickets.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $tickets->links() }}
    </div>
@endsection
