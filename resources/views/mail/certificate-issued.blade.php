@component('mail::message')
# Sertifikat Anda telah terbit

Halo {{ $user->name }},

Selamat! Anda telah menyelesaikan kursus **{{ $course->title }}**.

Nomor sertifikat: **{{ $certificate->number }}**

Sertifikat dapat diverifikasi secara publik menggunakan kode berikut:

{{ $certificate->verificationUrl() }}

@component('mail::button', ['url' => $certificate->verificationUrl()])
Lihat sertifikat
@endcomponent

Terima kasih,<br>
{{ config('app.name') }}
@endcomponent
