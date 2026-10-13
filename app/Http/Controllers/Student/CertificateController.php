<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CertificateController extends Controller
{
    public function index(Request $request): View
    {
        $certificates = Certificate::query()
            ->whereHas('enrollment', fn ($query) => $query->where('user_id', $request->user()->getKey()))
            ->with(['enrollment.course.instructor'])
            ->orderByDesc('issued_at')
            ->paginate(9);

        return view('student.certificates.index', compact('certificates'));
    }

    public function download(Request $request, Certificate $certificate): StreamedResponse
    {
        $this->authorize('download', $certificate);

        abort_if(blank($certificate->file_path), 404);

        return Storage::disk((string) config('kursus.certificate.disk'))
            ->download($certificate->file_path, $certificate->number.'.pdf');
    }
}
