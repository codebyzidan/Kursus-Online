<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Quiz;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->isInstructor()) {
            return redirect()->route('instructor.dashboard');
        }

        $user->forceFill(['last_seen_at' => now()])->save();

        $enrollments = Enrollment::query()
            ->where('user_id', $user->getKey())
            ->with(['course.instructor', 'course.category'])
            ->orderByDesc('enrolled_at')
            ->get();

        $stats = [
            'courses' => $enrollments->count(),
            'completed' => $enrollments->whereNotNull('completed_at')->count(),
            'inProgress' => $enrollments->whereNull('completed_at')->count(),
            'minutes' => (int) $enrollments->sum(fn (Enrollment $enrollment) => $enrollment->course->duration_minutes ?? 0),
        ];

        $continue = $enrollments
            ->whereNull('completed_at')
            ->sortByDesc('updated_at')
            ->first();

        $recommended = Course::query()
            ->published()
            ->whereNotIn('id', $enrollments->pluck('course_id'))
            ->with(['instructor', 'category'])
            ->orderByDesc('students_count')
            ->limit(3)
            ->get();

        $quizzes = Quiz::query()
            ->published()
            ->whereIn('course_id', $enrollments->pluck('course_id'))
            ->with('course')
            ->limit(4)
            ->get();

        return view('student.dashboard', compact('enrollments', 'stats', 'continue', 'recommended', 'quizzes'));
    }
}
