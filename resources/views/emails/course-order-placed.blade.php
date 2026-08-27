<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>New course purchase</title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; color: #1f2937; line-height: 1.5;">
    <h2 style="color: #0f2044; margin-bottom: 4px;">New course purchase</h2>
    <p style="margin-top: 0;">A student has just paid for a course on the TAP Security website.</p>

    <table cellpadding="6" cellspacing="0" style="border-collapse: collapse; min-width: 420px;">
        <tr><td style="border: 1px solid #e5e7eb; font-weight: bold;">Course</td><td style="border: 1px solid #e5e7eb;">{{ $course->titulo }}</td></tr>
        <tr><td style="border: 1px solid #e5e7eb; font-weight: bold;">Student</td><td style="border: 1px solid #e5e7eb;">{{ $order->name }}</td></tr>
        <tr><td style="border: 1px solid #e5e7eb; font-weight: bold;">Email</td><td style="border: 1px solid #e5e7eb;">{{ $order->email }}</td></tr>
        <tr><td style="border: 1px solid #e5e7eb; font-weight: bold;">Price</td><td style="border: 1px solid #e5e7eb;">${{ number_format((float) $order->price, 2) }} {{ strtoupper($order->currency ?? 'usd') }}</td></tr>
        @if (filled($order->cupon))
            <tr><td style="border: 1px solid #e5e7eb; font-weight: bold;">Coupon</td><td style="border: 1px solid #e5e7eb;">{{ $order->cupon }} (−${{ number_format((float) $order->cupon_mount, 2) }})</td></tr>
        @endif
        <tr><td style="border: 1px solid #e5e7eb; font-weight: bold;">Amount paid</td><td style="border: 1px solid #e5e7eb;"><strong>${{ number_format((float) $order->amount, 2) }} {{ strtoupper($order->currency ?? 'usd') }}</strong></td></tr>
        <tr><td style="border: 1px solid #e5e7eb; font-weight: bold;">Order ID</td><td style="border: 1px solid #e5e7eb;">{{ $order->order_id }}</td></tr>
        <tr><td style="border: 1px solid #e5e7eb; font-weight: bold;">Stripe payment</td><td style="border: 1px solid #e5e7eb;">{{ $order->txn_id }}</td></tr>
        <tr><td style="border: 1px solid #e5e7eb; font-weight: bold;">Date</td><td style="border: 1px solid #e5e7eb;">{{ optional($order->updated_at)->format('M j, Y H:i') }}</td></tr>
    </table>

    <p style="margin-top: 16px; color: #6b7280; font-size: 13px;">You can review the order and the student's enrollment in the admin panel.</p>
</body>
</html>
