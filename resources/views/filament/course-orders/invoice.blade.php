{{-- Factura de la orden, con la misma disposición que el admin anterior servía en
     backend/orders/show.blade.php, más los datos del cupón (#1417). --}}
@php
    $order = $getRecord();
    $profile = $order->user?->profile;
    $discount = (float) ($order->cupon_mount ?? 0);
    $paid = $order->amount !== null ? (float) $order->amount : (float) $order->price;
@endphp

<div class="fi-section rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
    <div class="flex flex-wrap items-baseline justify-between gap-2 border-b border-gray-200 pb-4 dark:border-white/10">
        <h2 class="text-xl font-bold">Tap Security</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400">
            Date: {{ $order->created_at?->format('M d, Y H:i:s') }}
        </p>
    </div>

    <div class="grid gap-6 py-6 sm:grid-cols-3">
        <div class="text-sm">
            <p class="mb-1 font-semibold text-gray-500 dark:text-gray-400">From</p>
            <p>11503 Jones Maltsberger Rd, Ste 1158</p>
            <p>San Antonio, TX 78216</p>
            <p>Phone: Tel: (210) 399-1116</p>
            <p>Email: admin@txassetpro.com</p>
        </div>

        <div class="text-sm">
            <p class="mb-1 font-semibold text-gray-500 dark:text-gray-400">To</p>
            <p class="font-semibold">{{ $order->name ?: $order->user?->name }}</p>
            @if ($profile?->address1)
                <p>{{ $profile->address1 }}</p>
            @endif
            @if ($profile?->city || $profile?->zipcode)
                <p>{{ collect([$profile?->city, $profile?->zipcode])->filter()->implode(', ') }}</p>
            @endif
            @if ($profile?->phone)
                <p>Phone: {{ $profile->phone }}</p>
            @endif
            <p>Email: {{ $order->email ?: $order->user?->email }}</p>
        </div>

        <div class="text-sm">
            <p class="font-semibold">Invoice #{{ $order->txn_id }}</p>
            <p class="mt-4"><span class="font-semibold">Order ID:</span> {{ $order->id }}</p>
            @if ($order->order_id)
                <p><span class="font-semibold">Order Nº:</span> {{ $order->order_id }}</p>
            @endif
            @if ($order->payment_status)
                <p><span class="font-semibold">Payment status:</span> {{ $order->payment_status }}</p>
            @endif
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-gray-200 dark:border-white/10">
                <tr>
                    <th class="py-2 pr-4 font-semibold">Qty</th>
                    <th class="py-2 pr-4 font-semibold">Product</th>
                    <th class="py-2 pr-4 font-semibold">Currency</th>
                    <th class="py-2 font-semibold">Price</th>
                </tr>
            </thead>
            <tbody>
                <tr class="border-b border-gray-100 dark:border-white/5">
                    <td class="py-2 pr-4">1</td>
                    <td class="py-2 pr-4">{{ $order->product_title }}</td>
                    <td class="py-2 pr-4">{{ strtoupper($order->currency ?: 'USD') }}</td>
                    <td class="py-2">$ {{ number_format((float) $order->price, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="grid gap-6 pt-6 sm:grid-cols-2">
        <div class="text-sm">
            <p class="mb-2 font-semibold text-gray-500 dark:text-gray-400">Payment Methods:</p>
            <p>Stripe — Visa, Mastercard, Maestro, American Express</p>
        </div>

        <div class="text-sm sm:justify-self-end sm:text-right">
            <table class="w-full">
                <tbody>
                    <tr>
                        <th class="py-1 pr-6 text-left font-semibold">Subtotal:</th>
                        <td class="py-1">${{ number_format((float) $order->price, 2) }}</td>
                    </tr>
                    @if (filled($order->cupon))
                        <tr>
                            <th class="py-1 pr-6 text-left font-semibold">Coupon:</th>
                            <td class="py-1">{{ $order->cupon }}</td>
                        </tr>
                        <tr>
                            <th class="py-1 pr-6 text-left font-semibold">Discount:</th>
                            <td class="py-1">- ${{ number_format($discount, 2) }}</td>
                        </tr>
                    @endif
                    <tr class="border-t border-gray-200 dark:border-white/10">
                        <th class="py-1 pr-6 text-left font-semibold">Total:</th>
                        <td class="py-1 font-semibold">${{ number_format($paid, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
