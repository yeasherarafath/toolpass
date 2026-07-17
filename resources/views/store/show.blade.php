@extends('layouts.app')

@section('title', $package->name)

@section('header')
    <div>
        <h2 class="page-title">{{ $package->name }}</h2>
        <div class="text-secondary">{{ $package->currency }} {{ number_format($package->price, 2) }} / {{ $package->duration_days }} days</div>
    </div>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <p>{{ $package->description }}</p>

            <h4>Tools included</h4>
            <ul>
                @forelse ($package->tools as $tool)
                    <li>{{ $tool->name }}</li>
                @empty
                    <li class="text-secondary">No tools linked.</li>
                @endforelse
            </ul>

            @if ($package->packageCustomFields->count())
                <h4>Required information</h4>
                <p class="text-secondary">You will be asked to provide this after placing the order.</p>
                <ul>
                    @foreach ($package->packageCustomFields as $field)
                        <li>{{ $field->label }} @if ($field->is_required)<span class="text-danger">*</span>@endif</li>
                    @endforeach
                </ul>
            @endif
        </div>
        <div class="card-footer">
            @auth
                @if (Auth::user()->role === 'customer')
                    <form method="POST" action="{{ route('customer.orders.store') }}">
                        @csrf
                        <input type="hidden" name="package_id" value="{{ $package->id }}">
                        <button type="submit" class="btn btn-primary">Place order</button>
                    </form>
                @else
                    <a href="{{ route('business.orders.index') }}" class="btn btn-primary">Manage orders</a>
                @endif
            @else
                <a href="{{ route('login') }}" class="btn btn-primary">Login to order</a>
            @endauth
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header"><h3 class="card-title">Reviews</h3></div>
        <div class="card-body">
            @forelse ($package->reviews()->where('status', 'approved')->get() as $review)
                <div class="mb-2 border p-2 rounded">
                    <div><strong>{{ $review->user?->name }}</strong> &middot; {{ $review->rating }}/5</div>
                    <div>{{ $review->title }}</div>
                    <div class="text-secondary">{{ $review->body }}</div>
                </div>
            @empty
                <p class="text-secondary">No reviews yet.</p>
            @endforelse

            @auth
                @if (Auth::user()->role === 'customer')
                    <form method="POST" action="{{ route('reviews.store') }}">
                        @csrf
                        <input type="hidden" name="package_id" value="{{ $package->id }}">
                        <div class="mb-2">
                            <label class="form-label">Rating</label>
                            <select name="rating" class="form-select">
                                @for ($i = 5; $i >= 1; $i--)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="mb-2">
                            <input type="text" name="title" class="form-control" placeholder="Title">
                        </div>
                        <div class="mb-2">
                            <textarea name="body" class="form-control" placeholder="Your review"></textarea>
                        </div>
                        <button class="btn btn-primary">Submit review</button>
                    </form>
                @endif
            @endauth
        </div>
    </div>
@endsection
