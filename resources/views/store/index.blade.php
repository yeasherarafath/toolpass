@extends('layouts.app')

@section('title', 'Store')

@section('header')
    <h2 class="page-title">Store</h2>
@endsection

@section('content')
    @if (!empty($banners) && $banners->count())
        <div id="offer-banners" class="carousel slide mb-4" data-bs-ride="carousel">
            <div class="carousel-inner rounded">
                @foreach ($banners as $i => $banner)
                    <div class="carousel-item {{ $i === 0 ? 'active' : '' }}">
                        @php($inner = $banner->image_path
                            ? '<img src="'.e(Storage::url($banner->image_path)).'" class="d-block w-100" alt="'.e($banner->title).'">'
                            : '<div class="d-flex align-items-center justify-content-center bg-primary-lt" style="height:220px"><span class="h2 mb-0">'.e($banner->title).'</span></div>')
                        @if ($banner->link)
                            <a href="{{ $banner->link }}">{!! $inner !!}</a>
                        @else
                            {!! $inner !!}
                        @endif
                        @if ($banner->title || $banner->description)
                            <div class="carousel-caption d-none d-md-block">
                                <h3>{{ $banner->title }}</h3>
                                @if ($banner->description)<p>{{ $banner->description }}</p>@endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
            @if ($banners->count() > 1)
                <button class="carousel-control-prev" type="button" data-bs-target="#offer-banners" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#offer-banners" data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                </button>
            @endif
        </div>
    @endif

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
