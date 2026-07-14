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
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-primary">Manage orders</a>
                @endif
            @else
                <a href="{{ route('login') }}" class="btn btn-primary">Login to order</a>
            @endauth
        </div>
    </div>
@endsection
