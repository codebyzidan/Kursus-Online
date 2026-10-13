<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use Illuminate\View\View;

class InstructorProfileController extends Controller
{
    public function show(User $instructor): View
    {
        abort_unless($instructor->isInstructor() || $instructor->isAdmin(), 404);

        $courses = Course::query()
            ->published()
            ->where('instructor_id', $instructor->getKey())
            ->with('category')
            ->orderByDesc('students_count')
            ->paginate(9);

        $stats = [
            'courses' => Course::query()->published()->where('instructor_id', $instructor->getKey())->count(),
            'students' => (int) Course::query()->published()->where('instructor_id', $instructor->getKey())->sum('students_count'),
            'rating' => round((float) Course::query()->published()->where('instructor_id', $instructor->getKey())->avg('rating_avg'), 1),
        ];

        return view('public.instructor', compact('instructor', 'courses', 'stats'));
    }
}
