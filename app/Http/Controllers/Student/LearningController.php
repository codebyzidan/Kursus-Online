<?php

namespace App\Http\Controllers\Student;

use App\Actions\Learning\RecordLessonProgress;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LearningController extends Controller
{
    public function index(Request $request): View
    {
        $enrollments = Enrollment::query()
            ->where('user_id', $request->user()->getKey())
            ->with(['course.instructor', 'course.category'])
            ->orderByDesc('updated_at')
            ->paginate(9);

        return view('student.courses.index', compact('enrollments'));
    }

    public function show(Request $request, Course $course): View
    {
        $this->authorize('learn', $course);

        $course->load(['sections.lessons', 'instructor']);

        $enrollment = $request->user()->enrollmentFor($course);

        $lessons = $course->sections->flatMap->lessons->where('is_published', true);
        $completedLessonIds = $request->user()->lessonProgress()
            ->where('course_id', $course->getKey())
            ->whereNotNull('completed_at')
            ->pluck('lesson_id')
            ->all();

        $current = $lessons->firstWhere('id', $enrollment?->last_lesson_id)
            ?? $lessons->first();

        return view('student.learning.show', compact('course', 'enrollment', 'current', 'completedLessonIds'));
    }

    public function lesson(Request $request, Course $course, Lesson $lesson): View
    {
        $this->authorize('view', $lesson);

        $course->load(['sections.lessons', 'instructor']);

        $enrollment = $request->user()->enrollmentFor($course);

        $completedLessonIds = $request->user()->lessonProgress()
            ->where('course_id', $course->getKey())
            ->whereNotNull('completed_at')
            ->pluck('lesson_id')
            ->all();

        return view('student.learning.lesson', compact('course', 'lesson', 'enrollment', 'completedLessonIds'));
    }

    public function progress(Request $request, Course $course, Lesson $lesson)
    {
        $this->authorize('view', $lesson);

        $validated = $request->validate([
            'completed' => ['sometimes', 'boolean'],
            'position' => ['sometimes', 'integer', 'min:0'],
        ]);

        app(RecordLessonProgress::class)->handle(
            $request->user(),
            $lesson,
            (bool) ($validated['completed'] ?? false),
            $validated['position'] ?? null,
        );

        if ($request->header('HX-Request') || $request->expectsJson()) {
            return response()->noContent();
        }

        return back()->with('success', 'Progres tersimpan.');
    }

    public function quizzes(Request $request, Course $course): View
    {
        $this->authorize('learn', $course);

        $quizzes = $course->quizzes()->published()->withCount('questions')->get();

        $attempts = $request->user()->quizAttempts()
            ->whereIn('quiz_id', $quizzes->pluck('id'))
            ->get()
            ->groupBy('quiz_id');

        return view('student.quizzes.index', compact('course', 'quizzes', 'attempts'));
    }
}
