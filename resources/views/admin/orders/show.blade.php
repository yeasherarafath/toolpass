@extends('layouts.app')

@section('title', 'Order ' . $order->order_number)

@section('header')
    <div>
        <h2 class="page-title">Order {{ $order->order_number }}</h2>
        <div class="text-secondary">{{ $order->package?->name }} &middot; {{ $order->user?->name }}</div>
    </div>
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row g-3">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Required information</h3>
                    <span class="badge bg-info">{{ $order->required_info_status }}</span>
                </div>
                <div class="card-body">
                    @if ($order->package->packageCustomFields->count())
                        <ul class="list-unstyled">
                            @foreach ($order->orderCustomFieldValues as $value)
                                <li><strong>{{ $value->field_label }}:</strong> {{ $value->value }}</li>
                            @endforeach
                        </ul>

                        @if ($order->required_info_status !== 'approved' && $order->required_info_status !== 'rejected')
                            <form method="POST" action="{{ route('business.orders.review-info', $order) }}">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">Reason (if rejecting)</label>
                                    <input type="text" name="reason" class="form-control">
                                </div>
                                <button name="decision" value="approve" class="btn btn-success">Approve</button>
                                <button name="decision" value="reject" class="btn btn-danger">Reject</button>
                            </form>
                        @endif
                    @else
                        <p class="text-secondary">This package does not require additional information.</p>
                    @endif
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header"><h3 class="card-title">Payments</h3></div>
                <div class="card-body">
                    @forelse ($order->payments as $payment)
                        <div class="mb-3 border p-2 rounded">
                            <div>{{ $payment->amount }} {{ $payment->currency }} &middot; {{ $payment->method }}</div>
                            <div class="text-secondary">{{ $payment->status }}</div>
                            @if ($payment->status === 'pending')
                                <form method="POST" action="{{ route('business.payments.verify', $payment) }}" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-success">Verify</button>
                                </form>
                                <form method="POST" action="{{ route('business.payments.reject', $payment) }}" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-danger">Reject</button>
                                </form>
                            @endif
                        </div>
                    @empty
                        <p class="text-secondary">No payments submitted.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Accesses</h3></div>
                <div class="card-body">
                    @forelse ($order->userToolAccesses as $access)
                        <div class="mb-3 border p-2 rounded">
                            <div><strong>{{ $access->tool?->name }}</strong>
                                <span class="badge bg-secondary">{{ $access->status }}</span>
                                <span class="badge bg-info">{{ $access->delivery_status }}</span>
                            </div>
                            @if ($access->toolAccount)
                                <div class="text-secondary">Account: {{ $access->toolAccount->name }}</div>
                            @endif
                            @if ($access->delivery_status !== 'delivered')
                                <form method="POST" action="{{ route('business.accesses.deliver', $access) }}">
                                    @csrf
                                    <button class="btn btn-sm btn-primary">Deliver</button>
                                </form>
                            @endif
                        </div>
                    @empty
                        <p class="text-secondary">No accesses created yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
