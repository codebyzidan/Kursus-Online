@component('mail::message')
# Kursus perlu perbaikan

Halo {{ $user->name }},

Kursus **{{ $course->title }}** belum dapat disetujui. Berikut catatan dari admin:

> {{ $reason }}

Silakan perbaiki lalu ajukan kembali untuk direview.

@component('mail::button', ['url' => url('/')])
Perbaiki kursus
@endcomponent

Terima kasih,<br>
{{ config('app.name') }}
@endcomponent
