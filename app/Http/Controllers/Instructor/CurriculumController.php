<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Http\Requests\LessonRequest;
use App\Http\Requests\SectionRequest;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Section;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CurriculumController extends Controller
{
    public function index(Course $course): View
    {
        $this->authorize('update', $course);

        $course->load(['sections.lessons']);

        return view('instructor.courses.curriculum', compact('course'));
    }

    public function storeSection(SectionRequest $request, Course $course): RedirectResponse
    {
        $this->authorize('update', $course);

        $course->sections()->create([
            'title' => $request->validated('title'),
            'position' => (int) $course->sections()->max('position') + 1,
        ]);

        return back()->with('success', 'Bagian ditambahkan.');
    }

    public function updateSection(SectionRequest $request, Course $course, Section $section): RedirectResponse
    {
        $this->authorize('update', $course);
        $this->ensureBelongsToCourse($course, $section);

        $section->update(['title' => $request->validated('title')]);

        return back()->with('success', 'Bagian diperbarui.');
    }

    public function destroySection(Course $course, Section $section): RedirectResponse
    {
        $this->authorize('update', $course);
        $this->ensureBelongsToCourse($course, $section);

        $section->delete();

        return back()->with('success', 'Bagian dihapus.');
    }

    public function reorderSections(Request $request, Course $course): JsonResponse
    {
        $this->authorize('update', $course);

        $request->validate(['order' => ['required', 'array'], 'order.*' => ['integer']]);

        foreach ($request->input('order') as $position => $id) {
            $course->sections()->whereKey($id)->update(['position' => $position + 1]);
        }

        return response()->json(['status' => 'ok']);
    }

    public function storeLesson(LessonRequest $request, Course $course, Section $section): RedirectResponse
    {
        $this->authorize('update', $course);
        $this->ensureBelongsToCourse($course, $section);

        $section->lessons()->create($request->validated() + [
            'position' => (int) $section->lessons()->max('position') + 1,
        ]);

        return back()->with('success', 'Materi ditambahkan.');
    }

    public function updateLesson(LessonRequest $request, Course $course, Section $section, Lesson $lesson): RedirectResponse
    {
        $this->authorize('update', $lesson);
        $this->ensureBelongsToCourse($course, $section);
        $this->ensureLessonBelongsToSection($section, $lesson);

        $lesson->update($request->validated());

        return back()->with('success', 'Materi diperbarui.');
    }

    public function destroyLesson(Course $course, Section $section, Lesson $lesson): RedirectResponse
    {
        $this->authorize('delete', $lesson);
        $this->ensureBelongsToCourse($course, $section);
        $this->ensureLessonBelongsToSection($section, $lesson);

        $lesson->delete();

        return back()->with('success', 'Materi dihapus.');
    }

    public function reorderLessons(Request $request, Course $course, Section $section): JsonResponse
    {
        $this->authorize('update', $course);
        $this->ensureBelongsToCourse($course, $section);

        $request->validate(['order' => ['required', 'array'], 'order.*' => ['integer']]);

        foreach ($request->input('order') as $position => $id) {
            $section->lessons()->whereKey($id)->update(['position' => $position + 1]);
        }

        return response()->json(['status' => 'ok']);
    }

    private function ensureBelongsToCourse(Course $course, Section $section): void
    {
        abort_unless($section->course_id === $course->getKey(), 404);
    }

    private function ensureLessonBelongsToSection(Section $section, Lesson $lesson): void
    {
        abort_unless($lesson->section_id === $section->getKey(), 404);
    }
}
