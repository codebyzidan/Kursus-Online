<?php

namespace App\Listeners;

use App\Events\CertificateIssued;
use App\Jobs\GenerateCertificatePdfJob;

class GenerateCertificatePdf
{
    public function handle(CertificateIssued $event): void
    {
        GenerateCertificatePdfJob::dispatch($event->certificate);
    }
}
