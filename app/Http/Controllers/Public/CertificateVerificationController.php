<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CertificateVerificationController extends Controller
{
    public function form(): View
    {
        return view('public.certificate-verify');
    }

    public function verify(Request $request): View
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:32'],
        ]);

        $certificate = Certificate::query()
            ->where('verification_code', strtoupper(trim($validated['code'])))
            ->with(['enrollment.user', 'enrollment.course.instructor'])
            ->first();

        return view('public.certificate-verify', [
            'certificate' => $certificate,
            'searched' => true,
        ]);
    }
}
