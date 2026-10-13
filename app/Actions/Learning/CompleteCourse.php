<?php

namespace App\Actions\Learning;

use App\Models\Certificate;
use App\Models\Enrollment;
use App\Services\CertificateService;

class CompleteCourse
{
    public function __construct(private readonly CertificateService $certificates) {}

    /**
     * Terbitkan sertifikat bila progres kursus sudah 100%.
     */
    public function handle(Enrollment $enrollment): ?Certificate
    {
        if (! $enrollment->isCompleted()) {
            return null;
        }

        return $this->certificates->issueFor($enrollment);
    }
}
