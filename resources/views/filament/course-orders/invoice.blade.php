{{-- Factura de la orden, con la disposición del admin de producción: remitente,
     destinatario y número de factura en tres columnas, la línea del producto, la forma
     de pago y el total, más los datos del cupón (#1417).

     Con estilos en línea y no con clases de Tailwind: el panel no compila un tema
     propio, así que las clases que Filament no usa no existen y la factura salía como
     texto plano. --}}
@php
    $order = $getRecord();
    $profile = $order->user?->profile;
    $discount = (float) ($order->cupon_mount ?? 0);
    $paid = $order->amount !== null ? (float) $order->amount : (float) $order->price;

    $card = 'background:#fff;border:1px solid rgba(3,7,18,.08);border-radius:12px;padding:24px;color:#111827;font-size:14px;line-height:1.55;';
    $muted = 'color:#6b7280;';
    $label = 'font-weight:600;';
    $th = 'text-align:left;padding:10px 12px;font-weight:600;border-bottom:1px solid #e5e7eb;background:#f9fafb;';
    $td = 'padding:10px 12px;border-bottom:1px solid #f3f4f6;';
@endphp

<div style="{{ $card }}">
    <div style="display:flex;flex-wrap:wrap;justify-content:space-between;align-items:baseline;gap:8px;padding-bottom:16px;border-bottom:1px solid #e5e7eb;">
        <h2 style="margin:0;font-size:20px;font-weight:700;">Tap Security</h2>
        <span style="{{ $muted }}">Date: {{ $order->created_at?->format('M d, Y H:i:s') }}</span>
    </div>

    <div style="display:flex;flex-wrap:wrap;gap:24px;padding:20px 0;">
        <div style="flex:1 1 200px;">
            <div style="{{ $muted }}{{ $label }}margin-bottom:4px;">From</div>
            <div style="{{ $label }}">Tap Security</div>
            <div>11503 Jones Maltsberger Rd, Ste 1158</div>
            <div>San Antonio, TX 78216</div>
            <div>Phone: Tel: (210) 399-1116</div>
            <div>Email: admin@txassetpro.com</div>
        </div>

        <div style="flex:1 1 200px;">
            <div style="{{ $muted }}{{ $label }}margin-bottom:4px;">To</div>
            <div style="{{ $label }}">{{ $order->name ?: $order->user?->name }}</div>
            @if ($profile?->address1)
                <div>{{ $profile->address1 }}</div>
            @endif
            @if ($profile?->city || $profile?->zipcode)
                <div>{{ collect([$profile?->city, $profile?->zipcode])->filter()->implode(', ') }}</div>
            @endif
            @if ($profile?->phone)
                <div>Phone: {{ $profile->phone }}</div>
            @endif
            <div>Email: {{ $order->email ?: $order->user?->email }}</div>
        </div>

        <div style="flex:1 1 200px;">
            <div style="{{ $label }}word-break:break-all;">Invoice #{{ $order->txn_id }}</div>
            <div style="margin-top:12px;"><span style="{{ $label }}">Order ID:</span> {{ $order->id }}</div>
            @if ($order->order_id)
                <div><span style="{{ $label }}">Order Nº:</span> {{ $order->order_id }}</div>
            @endif
            @if ($order->payment_status)
                <div><span style="{{ $label }}">Payment status:</span> {{ $order->payment_status }}</div>
            @endif
        </div>
    </div>

    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr>
                    <th style="{{ $th }}width:60px;">Qty</th>
                    <th style="{{ $th }}">Product</th>
                    <th style="{{ $th }}width:110px;">Currency</th>
                    <th style="{{ $th }}width:110px;text-align:right;">Price</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="{{ $td }}">1</td>
                    <td style="{{ $td }}">{{ $order->product_title }}</td>
                    <td style="{{ $td }}">{{ strtoupper($order->currency ?: 'USD') }}</td>
                    <td style="{{ $td }}text-align:right;">$ {{ number_format((float) $order->price, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div style="display:flex;flex-wrap:wrap;justify-content:space-between;gap:24px;padding-top:20px;">
        <div>
            <div style="{{ $muted }}{{ $label }}margin-bottom:4px;">Payment Methods:</div>
            <div>Stripe — Visa, Mastercard, Maestro, American Express</div>
        </div>

        <table style="border-collapse:collapse;min-width:240px;">
            <tbody>
                <tr>
                    <th style="text-align:left;padding:4px 24px 4px 0;{{ $label }}">Subtotal:</th>
                    <td style="padding:4px 0;text-align:right;">${{ number_format((float) $order->price, 2) }}</td>
                </tr>
                @if (filled($order->cupon))
                    <tr>
                        <th style="text-align:left;padding:4px 24px 4px 0;{{ $label }}">Coupon:</th>
                        <td style="padding:4px 0;text-align:right;">{{ $order->cupon }}</td>
                    </tr>
                    <tr>
                        <th style="text-align:left;padding:4px 24px 4px 0;{{ $label }}">Discount:</th>
                        <td style="padding:4px 0;text-align:right;">- ${{ number_format($discount, 2) }}</td>
                    </tr>
                @endif
                <tr>
                    <th style="text-align:left;padding:8px 24px 4px 0;border-top:1px solid #e5e7eb;font-weight:700;">Total:</th>
                    <td style="padding:8px 0 4px;border-top:1px solid #e5e7eb;text-align:right;font-weight:700;">${{ number_format($paid, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
