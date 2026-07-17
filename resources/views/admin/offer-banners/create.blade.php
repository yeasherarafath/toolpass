@extends('layouts.app')

@section('header')
    <h2 class="page-title">Add Offer Banner</h2>
@endsection

@section('content')
    <form method="POST" action="{{ route('admin.offer-banners.store') }}" enctype="multipart/form-data">
        @csrf
        @include('admin.offer-banners._form', ['banner' => null])
    </form>
@endsection
