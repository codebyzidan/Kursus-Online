@component('mail::message')
# Permintaan penarikan dana

Halo {{ $user->name }},

Instruktur **{{ $instructor->name }}** mengajukan penarikan dana sebesar **Rp{{ number_format($payout->amount, 0, ',', '.') }}**.

Bank: {{ $payout->bank_name }} — {{ $payout->bank_account_number }} ({{ $payout->bank_account_name }})

Silakan tinjau pengajuan ini pada panel admin.

Terima kasih,<br>
{{ config('app.name') }}
@endcomponent
