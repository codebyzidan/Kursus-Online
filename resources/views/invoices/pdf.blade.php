<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $order->code }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #0f172a; font-size: 12px; }
        .header { width: 100%; margin-bottom: 24px; }
        .header td { vertical-align: top; }
        .brand { font-size: 20px; font-weight: bold; }
        .muted { color: #64748b; }
        .status { text-align: right; }
        .status .pill { display: inline-block; padding: 4px 10px; border: 1px solid #0f172a; border-radius: 4px; font-weight: bold; }
        table.items { width: 100%; border-collapse: collapse; margin-top: 16px; }
        table.items th { text-align: left; border-bottom: 1px solid #cbd5e1; padding: 8px 4px; font-size: 11px; text-transform: uppercase; color: #64748b; }
        table.items td { padding: 8px 4px; border-bottom: 1px solid #e2e8f0; }
        table.items td.right, table.items th.right { text-align: right; }
        table.totals { width: 45%; margin-left: 55%; margin-top: 12px; }
        table.totals td { padding: 4px 0; }
        table.totals td.right { text-align: right; }
        .grand td { font-weight: bold; border-top: 1px solid #0f172a; padding-top: 8px; }
        .footer { margin-top: 32px; color: #64748b; font-size: 11px; }
    </style>
</head>
<body>
    <table class="header">
        <tr>
            <td>
                <div class="brand">{{ config('app.name') }}</div>
                <div class="muted">{{ config('app.name') }} — Platform kursus online</div>
            </td>
            <td class="status">
                <div class="pill">{{ $order->status->label() }}</div>
                <div class="muted" style="margin-top:6px;">Invoice: {{ $order->code }}</div>
                <div class="muted">Tanggal: {{ $order->created_at?->translatedFormat('d F Y') }}</div>
            </td>
        </tr>
    </table>

    <div>
        <strong>Ditagihkan kepada</strong><br>
        {{ $order->user->name }}<br>
        <span class="muted">{{ $order->user->email }}</span>
    </div>

    <table class="items">
        <thead>
            <tr>
                <th>Kursus</th>
                <th class="right">Harga</th>
                <th class="right">Diskon</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $item)
                <tr>
                    <td>{{ $item->course_title }}</td>
                    <td class="right">Rp{{ number_format($item->price, 0, ',', '.') }}</td>
                    <td class="right">-Rp{{ number_format($item->discount, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <td>Subtotal</td>
            <td class="right">Rp{{ number_format($order->subtotal, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Diskon{{ $order->coupon ? ' ('.$order->coupon->code.')' : '' }}</td>
            <td class="right">-Rp{{ number_format($order->discount, 0, ',', '.') }}</td>
        </tr>
        <tr class="grand">
            <td>Total</td>
            <td class="right">Rp{{ number_format($order->total, 0, ',', '.') }}</td>
        </tr>
    </table>

    <div class="footer">
        Dibayar pada: {{ $order->paid_at?->translatedFormat('d F Y H:i') ?? '—' }}
        @if ($order->payment_channel)
            <br>Metode: {{ $order->payment_channel->label() }}
        @endif
        <br>Invoice ini dibuat otomatis oleh sistem {{ config('app.name') }}.
    </div>
</body>
</html>
