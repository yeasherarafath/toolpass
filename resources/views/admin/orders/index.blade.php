@extends('layouts.app')

@section('title', 'Orders')

@section('header')
    <h2 class="page-title">Orders</h2>
@endsection

@section('content')
    <div class="card">
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Customer</th>
                        <th>Package</th>
                        <th>Status</th>
                        <th>Payment</th>
                        <th>Info</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr>
                            <td>{{ $order->order_number }}</td>
                            <td>{{ $order->user?->name }}</td>
                            <td>{{ $order->package?->name }}</td>
                            <td><span class="badge bg-secondary">{{ $order->order_status }}</span></td>
                            <td><span class="badge bg-{{ $order->payment_status === 'paid' ? 'success' : 'warning' }}">{{ $order->payment_status }}</span></td>
                            <td><span class="badge bg-info">{{ $order->required_info_status }}</span></td>
                            <td class="text-end">
                                <a href="{{ route('business.orders.show', $order) }}" class="btn btn-sm btn-primary">Manage</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-secondary">No orders.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $orders->links() }}
    </div>
@endsection
