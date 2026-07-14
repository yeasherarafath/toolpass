@extends('layouts.app')

@section('title', 'Order ' . $order->order_number)

@section('header')
    <div>
        <h2 class="page-title">Order {{ $order->order_number }}</h2>
        <div class="text-secondary">{{ $order->package?->name }}</div>
    </div>
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row g-3">
        <div class="col-md-7">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Required information</h3>
                    <span class="badge bg-info">{{ $order->required_info_status }}</span>
                </div>
                <div class="card-body">
                    @if (in_array($order->required_info_status, ['not_required', 'approved']))
                        <p class="text-secondary">No further action needed.</p>
                    @else
                        <form method="POST" action="{{ route('customer.orders.info', $order) }}">
                            @csrf
                            @foreach ($order->package->packageCustomFields as $field)
                                <div class="mb-3">
                                    <label class="form-label">{{ $field->label }} @if ($field->is_required)<span class="text-danger">*</span>@endif</label>
                                    <input type="text" name="fields[{{ $field->id }}]"
                                           class="form-control"
                                           value="{{ old('fields.' . $field->id, $order->orderCustomFieldValues->where('package_custom_field_id', $field->id)->first()?->value) }}">
                                </div>
                            @endforeach
                            <button class="btn btn-primary">Submit information</button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">Payment</h3>
                    <span class="badge bg-{{ $order->payment_status === 'paid' ? 'success' : 'warning' }}">{{ $order->payment_status }}</span>
                </div>
                <div class="card-body">
                    @if ($order->payment_status === 'paid')
                        <p class="text-secondary">Payment received. Thank you.</p>
                    @else
                        <form method="POST" action="{{ route('customer.orders.payments', $order) }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Amount ({{ $order->currency }})</label>
                                <input type="number" step="0.01" name="amount" class="form-control" value="{{ $order->payable_amount }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Method</label>
                                <select name="method" class="form-select">
                                    <option value="bkash">bKash</option>
                                    <option value="nagad">Nagad</option>
                                    <option value="rocket">Rocket</option>
                                    <option value="card">Card</option>
                                    <option value="bank">Bank</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Sender number</label>
                                <input type="text" name="sender_number" class="form-control">
                            </div>
                            <button class="btn btn-primary">Submit payment</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Your accesses</h3></div>
                <div class="card-body">
                    @forelse ($order->userToolAccesses as $access)
                        <div class="mb-3 border p-2 rounded">
                            <div>
                                <strong>{{ $access->tool?->name }}</strong>
                                <span class="badge bg-secondary">{{ $access->status }}</span>
                                <span class="badge bg-info">{{ $access->delivery_status }}</span>
                            </div>
                            @if ($access->delivery_status === 'delivered')
                                <form method="POST" action="{{ route('customer.otp.request') }}" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="user_tool_access_id" value="{{ $access->id }}">
                                    <button class="btn btn-sm btn-warning">Request OTP</button>
                                </form>
                                <form method="POST" action="{{ route('customer.devices.reset') }}" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="user_tool_access_id" value="{{ $access->id }}">
                                    <button class="btn btn-sm btn-outline-danger">Reset device</button>
                                </form>
                            @endif
                        </div>
                    @empty
                        <p class="text-secondary">Accesses will appear here after payment is verified.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
