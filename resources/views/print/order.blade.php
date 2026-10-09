@extends('print.layout', ['title' => 'Order #' . $order->id])

@section('content')
    @php
        $profile = $order->user?->profile;
        $discount = (float) ($order->cupon_mount ?? 0);
        $paid = $order->amount !== null ? (float) $order->amount : (float) $order->price;
    @endphp

    <h1>TAP Security</h1>
    <p class="muted">Order #{{ $order->id }} · {{ $order->created_at?->timezone(config('app.admin_timezone'))?->format('M d, Y H:i') }}</p>

    <h2>Customer</h2>
    <table>
        <tr><th>Name</th><td>{{ $order->name ?: $order->user?->name }}</td></tr>
        <tr><th>Email</th><td>{{ $order->email ?: $order->user?->email }}</td></tr>
        @if ($profile?->phone)
            <tr><th>Phone</th><td>{{ $profile->phone }}</td></tr>
        @endif
        @if ($profile?->address)
            <tr><th>Address</th><td>{{ $profile->address }} {{ $profile->city }} {{ $profile->state }} {{ $profile->zipcode }}</td></tr>
        @endif
    </table>

    <h2>Order</h2>
    <table>
        <tr><th>Order ID</th><td>{{ $order->order_id }}</td></tr>
        <tr><th>Product</th><td>{{ $order->product_title }}</td></tr>
        <tr><th>Price</th><td>${{ number_format((float) $order->price, 2) }} {{ strtoupper($order->currency ?? 'usd') }}</td></tr>
        @if ($order->cupon)
            <tr><th>Coupon</th><td>{{ $order->cupon }}</td></tr>
            <tr><th>Discount</th><td>-${{ number_format($discount, 2) }}</td></tr>
        @endif
        <tr><th>Paid</th><td><strong>${{ number_format($paid, 2) }}</strong></td></tr>
        <tr><th>Payment status</th><td>{{ $order->payment_status }}</td></tr>
        <tr><th>Transaction</th><td>{{ $order->txn_id }}</td></tr>
    </table>
@endsection
