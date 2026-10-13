<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Sertifikat {{ $certificate->number }}</title>
    <style>
        @page { margin: 0; }
        body { font-family: DejaVu Sans, sans-serif; color: #0f172a; margin: 0; }
        .frame { border: 3px solid #0f172a; margin: 24px; padding: 40px 56px; height: 460px; }
        .eyebrow { font-size: 11px; letter-spacing: 4px; text-transform: uppercase; color: #64748b; }
        .title { font-size: 42px; font-weight: bold; letter-spacing: 6px; margin: 8px 0 0; }
        .subtitle { font-size: 15px; color: #475569; margin-top: 4px; }
        .lead { font-size: 12px; color: #64748b; margin: 28px 0 4px; }
        .name { font-size: 30px; font-weight: bold; border-bottom: 1px solid #cbd5e1; display: inline-block; padding-bottom: 6px; }
        .course { font-size: 19px; font-weight: bold; margin-top: 10px; }
        .meta { font-size: 11px; color: #64748b; margin-top: 18px; }
        .footer { width: 100%; margin-top: 36px; }
        .footer td { vertical-align: bottom; }
        .sig { font-size: 12px; text-align: center; width: 40%; }
        .sig .line { border-top: 1px solid #94a3b8; margin-top: 44px; padding-top: 6px; }
        .qr { text-align: right; }
        .qr-note { font-size: 10px; color: #64748b; margin-top: 4px; }
    </style>
</head>
<body>
    <div class="frame">
        <div class="eyebrow">{{ config('app.name') }}</div>
        <div class="title">SERTIFIKAT</div>
        <div class="subtitle">Penyelesaian Kursus</div>

        <p class="lead">Diberikan kepada</p>
        <div class="name">{{ $student->name }}</div>

        <p class="lead">atas keberhasilan menyelesaikan kursus</p>
        <div class="course">{{ $course->title }}</div>

        <p class="meta">
            Nomor: {{ $certificate->number }}
            &nbsp;·&nbsp; Diterbitkan {{ $certificate->issued_at?->translatedFormat('d F Y') }}
        </p>

        <table class="footer">
            <tr>
                <td class="sig">
                    <div class="line">Instruktur: {{ $course->instructor?->name }}</div>
                </td>
                <td class="qr">
                    @if ($qrDataUri)
                        <img src="{{ $qrDataUri }}" width="110" alt="QR verifikasi">
                        <div class="qr-note">Pindai untuk verifikasi</div>
                    @endif
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
