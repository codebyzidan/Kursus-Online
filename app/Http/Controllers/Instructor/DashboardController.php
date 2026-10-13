<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Services\InstructorEarningsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request, InstructorEarningsService $earnings): View
    {
        $instructor = $request->user();

        $courses = $instructor->courses()
            ->withCount('enrollments')
            ->orderByDesc('created_at')
            ->get();

        $stats = [
            'courses' => $courses->count(),
            'published' => $courses->filter(fn ($course) => $course->isPublished())->count(),
            'students' => (int) $courses->sum('enrollments_count'),
            'rating' => round((float) $courses->avg('rating_avg'), 1),
            'earnings' => $earnings->totalEarnings($instructor),
            'balance' => $earnings->availableBalance($instructor),
        ];

        $months = collect(range(5, 0))->map(function (int $offset) use ($earnings, $instructor) {
            $date = now()->startOfMonth()->subMonths($offset);

            return [
                'label' => $date->translatedFormat('M Y'),
                'value' => $earnings->earningsBetween($instructor, $date->copy()->startOfMonth(), $date->copy()->endOfMonth()),
            ];
        });

        $chart = [
            'type' => 'line',
            'data' => [
                'labels' => $months->pluck('label'),
                'datasets' => [[
                    'label' => 'Pendapatan',
                    'data' => $months->pluck('value'),
                    'fill' => false,
                ]],
            ],
        ];

        $recentEnrollments = Enrollment::query()
            ->whereIn('course_id', $courses->pluck('id'))
            ->with(['user', 'course'])
            ->latest('enrolled_at')
            ->limit(6)
            ->get();

        return view('instructor.dashboard', compact('courses', 'stats', 'chart', 'recentEnrollments'));
    }
}
