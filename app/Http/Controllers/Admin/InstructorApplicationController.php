<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Instructor\ApproveInstructorApplication;
use App\Actions\Instructor\RejectInstructorApplication;
use App\Http\Controllers\Controller;
use App\Models\InstructorApplication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InstructorApplicationController extends Controller
{
    public function index(Request $request): View
    {
        $applications = InstructorApplication::query()
            ->with('user')
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.instructor-applications.index', compact('applications'));
    }

    public function approve(Request $request, InstructorApplication $application, ApproveInstructorApplication $action): RedirectResponse
    {
        $action->handle($application, $request->user());

        return back()->with('success', 'Pengajuan disetujui; pengguna kini berperan sebagai instruktur.');
    }

    public function reject(Request $request, InstructorApplication $application, RejectInstructorApplication $action): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $action->handle($application, $request->user(), $validated['reason']);

        return back()->with('success', 'Pengajuan ditolak.');
    }
}
