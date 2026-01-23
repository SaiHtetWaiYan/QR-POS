<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt #{{ $order->order_no }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            width: 80mm;
            margin: 0;
            padding: 5mm;
            font-size: 12px;
            color: #000;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .mb-2 { margin-bottom: 5px; }
        .border-b { border-bottom: 1px dashed #000; padding-bottom: 5px; margin-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; vertical-align: top; }
        td.price { text-align: right; }
    </style>
</head>
<body onload="window.print()">
    <div class="text-center mb-2">
        <h2 class="font-bold" style="margin:0;">{{ config('app.name') }}</h2>
        <p style="margin:0;">123 Restaurant Street</p>
        <p style="margin:0;">Tel: 555-0123</p>
    </div>

    <div class="border-b">
        <p>Order: #{{ $order->order_no }}</p>
        <p>Table: {{ $order->table->name }}</p>
        <p>Date: {{ $order->created_at->format('Y-m-d H:i') }}</p>
    </div>

    <table class="mb-2">
        @foreach($order->orderItems as $item)
            <tr>
                <td colspan="2">{{ $item->qty }}x {{ $item->name_snapshot }}</td>
            </tr>
            <tr>
                <td style="padding-left: 10px; color: #555;">
                     @if($item->note) ({{ $item->note }}) @endif
                </td>
                <td class="price">{{ number_format($item->line_total, 2) }}</td>
            </tr>
        @endforeach
    </table>

    <div class="border-b"></div>

    <table>
        <tr>
            <td>Subtotal</td>
            <td class="price">{{ number_format($order->subtotal, 2) }}</td>
        </tr>
        <tr>
            <td>Tax</td>
            <td class="price">{{ number_format($order->tax, 2) }}</td>
        </tr>
        <tr>
            <td>Service</td>
            <td class="price">{{ number_format($order->service_charge, 2) }}</td>
        </tr>
        @if($order->discount_amount > 0)
            <tr>
                <td>Discount</td>
                <td class="price">-{{ number_format($order->discount_amount, 2) }}</td>
            </tr>
        @endif
        <tr class="font-bold" style="font-size: 14px;">
            <td style="padding-top: 5px;">TOTAL</td>
            <td class="price" style="padding-top: 5px;">{{ config('pos.currency_symbol') }}{{ number_format($order->total, 2) }}</td>
        </tr>
    </table>

    <div class="text-center" style="margin-top: 20px;">
        <p>Thank you for dining with us!</p>
        <p>Wifi: Guest / Pass123</p>
    </div>
</body>
</html>
