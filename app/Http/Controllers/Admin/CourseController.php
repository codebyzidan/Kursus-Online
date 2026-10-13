<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Course\ApproveCourse;
use App\Actions\Course\RejectCourse;
use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function index(Request $request): View
    {
        $courses = Course::query()
            ->with(['instructor', 'category'])
            ->withCount(['sections', 'lessons'])
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->when($request->filled('q'), fn ($query) => $query->search($request->string('q')->toString()))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.courses.index', compact('courses'));
    }

    public function show(Course $course): View
    {
        $course->load(['instructor', 'category', 'sections.lessons']);

        return view('admin.courses.show', compact('course'));
    }

    public function approve(Course $course, ApproveCourse $action): RedirectResponse
    {
        $action->handle($course);

        return back()->with('success', 'Kursus diterbitkan.');
    }

    public function reject(Request $request, Course $course, RejectCourse $action): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $action->handle($course, $validated['reason']);

        return back()->with('success', 'Kursus ditolak dan instruktur diberi tahu.');
    }
}
