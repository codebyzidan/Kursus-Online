<?php

namespace App\Jobs;

use App\Models\Certificate;
use App\Notifications\CertificateIssuedNotification;
use App\Services\CertificateService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateCertificatePdfJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public Certificate $certificate) {}

    public function handle(CertificateService $certificates): void
    {
        $certificates->renderPdf($this->certificate);

        $student = $this->certificate->enrollment->user;

        if ($student !== null) {
            $student->notify(new CertificateIssuedNotification($this->certificate->fresh()));
        }
    }
}
