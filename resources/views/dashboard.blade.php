@extends('layouts.app')

@section('header')
    <h2 class="page-title">Dashboard</h2>
@endsection

@section('content')
    <div class="alert alert-info">
        Signed in as <strong>{{ auth()->user()->name }}</strong>
        (role: {{ auth()->user()->role }}).
    </div>
    <div class="row row-deck row-cards">
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="text-secondary">Role</div>
                    <div class="h1">{{ ucfirst(auth()->user()->role) }}</div>
                </div>
            </div>
        </div>
    </div>
@endsection
