@component('mail::message')
# Selamat, Anda kini menjadi instruktur

Halo {{ $user->name }},

Pengajuan Anda untuk menjadi instruktur telah disetujui. Anda kini dapat membuat kursus, mengunggah materi, dan mengatur harga.

@component('mail::button', ['url' => url('/')])
Buat kursus pertama
@endcomponent

Terima kasih,<br>
{{ config('app.name') }}
@endcomponent
