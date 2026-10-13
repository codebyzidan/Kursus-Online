<?php

namespace Database\Factories;

use App\Models\Certificate;
use App\Models\Enrollment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Certificate>
 */
class CertificateFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'enrollment_id' => Enrollment::factory()->completed(),
            'number' => 'CERT-'.now()->format('Ymd').'-'.Str::upper(Str::random(6)),
            'verification_code' => Str::upper(Str::random(12)),
            'file_path' => null,
            'issued_at' => now(),
        ];
    }
}
