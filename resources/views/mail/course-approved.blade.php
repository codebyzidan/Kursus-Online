@component('mail::message')
# Kursus disetujui

Halo {{ $user->name }},

Kursus **{{ $course->title }}** telah disetujui dan kini tayang di katalog.

Siswa sudah dapat menemukan dan membeli kursus Anda.

@component('mail::button', ['url' => url('/')])
Lihat kursus
@endcomponent

Terima kasih,<br>
{{ config('app.name') }}
@endcomponent
