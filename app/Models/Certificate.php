<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\URL;

/**
 * @property-read Enrollment $enrollment
 * @property-read User|null $student
 * @property-read Course|null $course
 */
class Certificate extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = ['enrollment_id', 'number', 'verification_code', 'file_path', 'issued_at'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return ['issued_at' => 'datetime'];
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function getStudentAttribute(): ?User
    {
        return $this->enrollment?->user;
    }

    public function getCourseAttribute(): ?Course
    {
        return $this->enrollment?->course;
    }

    public function verificationUrl(): string
    {
        return URL::to('/sertifikat/verifikasi/'.$this->verification_code);
    }
}
