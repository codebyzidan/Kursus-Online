<?php

namespace App\Http\Controllers\Instructor;

use App\Actions\Course\CreateCourse;
use App\Actions\Course\SubmitCourseForReview;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Models\Category;
use App\Models\Course;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function index(Request $request): View
    {
        $courses = $request->user()->courses()
            ->with(['category'])
            ->withCount('enrollments')
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('instructor.courses.index', compact('courses'));
    }

    public function create(): View
    {
        $categories = Category::query()->active()->orderBy('name')->get();

        return view('instructor.courses.create', compact('categories'));
    }

    public function store(StoreCourseRequest $request, CreateCourse $action): RedirectResponse
    {
        $course = $action->handle($request->user(), $request->validated());

        return redirect()
            ->route('instructor.courses.curriculum', $course)
            ->with('success', 'Kursus draf dibuat. Lanjutkan dengan menyusun kurikulum.');
    }

    public function edit(Course $course): View
    {
        $this->authorize('update', $course);

        $categories = Category::query()->active()->orderBy('name')->get();
        $course->load(['sections.lessons']);

        return view('instructor.courses.edit', compact('course', 'categories'));
    }

    public function update(UpdateCourseRequest $request, Course $course): RedirectResponse
    {
        $this->authorize('update', $course);

        $course->update($request->validated());

        return back()->with('success', 'Informasi kursus disimpan.');
    }

    public function destroy(Course $course): RedirectResponse
    {
        $this->authorize('delete', $course);

        $course->delete();

        return redirect()
            ->route('instructor.courses.index')
            ->with('success', 'Kursus dihapus.');
    }

    public function submit(Request $request, Course $course, SubmitCourseForReview $action): RedirectResponse
    {
        $this->authorize('submitForReview', $course);

        $action->handle($course);

        return redirect()
            ->route('instructor.courses.index')
            ->with('success', 'Kursus diajukan untuk review admin.');
    }
}
