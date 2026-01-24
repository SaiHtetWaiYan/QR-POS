<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title></title>
    <style>
        @page {
            size: 210mm 99mm;
            margin: 6mm;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            width: 210mm;
            margin: 0;
            padding: 6mm;
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
        <h2 class="font-bold" style="margin:0;">{{ config('pos.shop_name', config('app.name', 'QR POS')) }}</h2>
        <p style="margin:0;">{{ config('pos.shop_address', '') }}</p>
        <p style="margin:0;">{{ config('pos.shop_phone', '') }}</p>
    </div>

    <div class="border-b">
        <p>{{ __('Order') }}: #{{ $order->order_no }}</p>
        <p>{{ __('Table') }}: {{ $order->table->name }}</p>
        <p>{{ __('Date') }}: {{ $order->created_at->format('Y-m-d H:i') }}</p>
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
            <td>{{ __('Subtotal') }}</td>
            <td class="price">{{ number_format($order->subtotal, 2) }}</td>
        </tr>
        <tr>
            <td>{{ __('Tax') }}</td>
            <td class="price">{{ number_format($order->tax, 2) }}</td>
        </tr>
        <tr>
            <td>{{ __('Service') }}</td>
            <td class="price">{{ number_format($order->service_charge, 2) }}</td>
        </tr>
        @if($order->coupon_amount > 0)
            <tr>
                <td>{{ __('Coupon') }}</td>
                <td class="price">-{{ number_format($order->coupon_amount, 2) }}</td>
            </tr>
        @endif
        <tr class="font-bold" style="font-size: 14px;">
            <td style="padding-top: 5px;">{{ __('TOTAL') }}</td>
            <td class="price" style="padding-top: 5px;">{{ config('pos.currency_symbol') }}{{ number_format($order->total, 2) }}</td>
        </tr>
    </table>

    <div class="text-center" style="margin-top: 20px;">
        <p>{{ __('Thank you for dining with us!') }}</p>
    </div>
</body>
</html>
