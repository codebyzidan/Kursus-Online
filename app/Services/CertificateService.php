<?php

namespace App\Services;

use App\Events\CertificateIssued;
use App\Models\Certificate;
use App\Models\Enrollment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CertificateService
{
    /**
     * Terbitkan (idempotent) sertifikat untuk enrollment yang sudah selesai.
     */
    public function issueFor(Enrollment $enrollment): Certificate
    {
        $certificate = Certificate::firstOrCreate(
            ['enrollment_id' => $enrollment->getKey()],
            [
                'number' => $this->generateNumber(),
                'verification_code' => $this->generateVerificationCode(),
                'issued_at' => now(),
            ],
        );

        if ($certificate->wasRecentlyCreated) {
            CertificateIssued::dispatch($certificate);
        }

        return $certificate;
    }

    /**
     * Render PDF sertifikat ke disk privat dan simpan lokasinya.
     */
    public function renderPdf(Certificate $certificate): string
    {
        $certificate->loadMissing(['enrollment.user', 'enrollment.course']);

        $qrDataUri = $this->qrDataUri($certificate->verificationUrl());

        $pdf = Pdf::loadView('certificates.pdf', [
            'certificate' => $certificate,
            'student' => $certificate->enrollment->user,
            'course' => $certificate->enrollment->course,
            'qrDataUri' => $qrDataUri,
        ])->setPaper('a4', 'landscape');

        $path = trim((string) config('kursus.certificate.path'), '/')
            .'/'.$certificate->number.'.pdf';

        Storage::disk((string) config('kursus.certificate.disk'))->put($path, $pdf->output());

        $certificate->forceFill(['file_path' => $path])->save();

        return $path;
    }

    public function generateNumber(): string
    {
        do {
            $number = 'CERT-'.now()->format('Ymd').'-'.Str::upper(Str::random(6));
        } while (Certificate::query()->where('number', $number)->exists());

        return $number;
    }

    public function generateVerificationCode(): string
    {
        do {
            $code = Str::upper(Str::random(12));
        } while (Certificate::query()->where('verification_code', $code)->exists());

        return $code;
    }

    private function qrDataUri(string $url): ?string
    {
        try {
            $png = QrCode::format('png')
                ->size(180)
                ->margin(0)
                ->generate($url);

            return 'data:image/png;base64,'.base64_encode($png);
        } catch (\Throwable) {
            return null;
        }
    }
}
