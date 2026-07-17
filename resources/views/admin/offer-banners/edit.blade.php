@extends('layouts.app')

@section('header')
    <h2 class="page-title">Edit Offer Banner</h2>
@endsection

@section('content')
    <form method="POST" action="{{ route('business.offer-banners.update', $banner) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.offer-banners._form', ['banner' => $banner])
    </form>
@endsection
