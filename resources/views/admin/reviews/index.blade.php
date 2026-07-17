@extends('layouts.app')

@section('title', 'Reviews')

@section('header')
    <h2 class="page-title">Reviews</h2>
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="table-responsive">
            <table class="table card-table">
                <thead><tr><th>Customer</th><th>Package</th><th>Rating</th><th>Status</th><th></th></tr></thead>
                <tbody>
                    @forelse ($reviews as $review)
                        <tr>
                            <td>{{ $review->user?->name }}</td>
                            <td>{{ $review->package?->name }}</td>
                            <td>{{ $review->rating }}/5</td>
                            <td><span class="badge bg-secondary">{{ $review->status }}</span></td>
                            <td>
                                @if ($review->status === 'pending')
                                    <form method="POST" action="{{ route('business.reviews.moderate', $review) }}" class="d-inline">
                                        @csrf
                                        <button name="decision" value="approve" class="btn btn-sm btn-success">Approve</button>
                                        <button name="decision" value="reject" class="btn btn-sm btn-danger">Reject</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-secondary">No reviews.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $reviews->links() }}
    </div>
@endsection
