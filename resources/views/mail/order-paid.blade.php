@component('mail::message')
# Pembayaran diterima

Halo {{ $user->name }},

Terima kasih. Pembayaran untuk pesanan **{{ $order->code }}** telah kami terima dan akses kursus Anda sudah aktif.

@component('mail::table')
| Kursus | Harga |
|:--|--:|
@foreach ($order->items as $item)
| {{ $item->course_title }} | Rp{{ number_format($item->net_price, 0, ',', '.') }} |
@endforeach
@endcomponent

**Total dibayar: Rp{{ number_format($order->total, 0, ',', '.') }}**

@component('mail::button', ['url' => url('/')])
Mulai belajar
@endcomponent

Invoice PDF tersimpan pada dashboard pesanan Anda.

Terima kasih,<br>
{{ config('app.name') }}
@endcomponent
