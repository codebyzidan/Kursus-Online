<?php

namespace App\Http\Controllers\Student;

use App\Actions\Instructor\ApplyAsInstructor;
use App\Http\Controllers\Controller;
use App\Http\Requests\InstructorApplicationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InstructorApplicationController extends Controller
{
    public function create(Request $request): View
    {
        abort_if($request->user()->isInstructor(), 403, 'Anda sudah menjadi instruktur.');

        $application = $request->user()->instructorApplication;

        return view('student.instructor-application', compact('application'));
    }

    public function store(InstructorApplicationRequest $request, ApplyAsInstructor $action): RedirectResponse
    {
        $action->handle($request->user(), $request->validated());

        return redirect()
            ->route('student.instructor-application.create')
            ->with('success', 'Pengajuan instruktur berhasil dikirim dan sedang ditinjau admin.');
    }
}
