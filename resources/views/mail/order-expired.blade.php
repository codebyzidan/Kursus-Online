@component('mail::message')
# Pesanan kedaluwarsa

Halo {{ $user->name }},

Pesanan **{{ $order->code }}** belum dibayar hingga batas waktu dan kini berstatus kedaluwarsa.

Anda dapat membuat pesanan baru kapan saja dari katalog kursus.

@component('mail::button', ['url' => url('/')])
Buka katalog
@endcomponent

Terima kasih,<br>
{{ config('app.name') }}
@endcomponent
