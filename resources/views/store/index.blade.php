@extends('layouts.app')

@section('title', 'Store')

@section('header')
    <h2 class="page-title">Store</h2>
@endsection

@section('content')
    <div class="row row-cards">
        @forelse ($packages as $package)
            <div class="col-sm-6 col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">{{ $package->name }}</h3>
                        <p class="text-secondary">{{ Str::limit($package->description, 120) }}</p>
                        <div class="d-flex align-items-center">
                            <span class="h2 mb-0">{{ $package->currency }} {{ number_format($package->price, 2) }}</span>
                            <span class="ms-2 text-secondary">/ {{ $package->duration_days }} days</span>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('store.show', $package) }}" class="btn btn-primary w-100">View details</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="empty">
                    <p class="empty-title">No packages available</p>
                </div>
            </div>
        @endforelse
    </div>
@endsection
