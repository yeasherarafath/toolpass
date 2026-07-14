@extends('layouts.app')

@section('title', 'Support Tickets')

@section('header')
    <h2 class="page-title">Support Tickets</h2>
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="table-responsive">
            <table class="table card-table">
                <thead><tr><th>Ticket</th><th>Customer</th><th>Subject</th><th>Status</th><th></th></tr></thead>
                <tbody>
                    @forelse ($tickets as $ticket)
                        <tr>
                            <td>{{ $ticket->ticket_number }}</td>
                            <td>{{ $ticket->user?->name }}</td>
                            <td>{{ $ticket->subject }}</td>
                            <td><span class="badge bg-secondary">{{ $ticket->status }}</span></td>
                            <td><a href="{{ route('admin.support.show', $ticket) }}" class="btn btn-sm btn-primary">Manage</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-secondary">No tickets.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $tickets->links() }}
    </div>
@endsection
