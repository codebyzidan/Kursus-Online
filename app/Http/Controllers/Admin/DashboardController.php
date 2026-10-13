<?php

namespace App\Http\Controllers\Admin;

use App\Enums\CourseStatus;
use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $stats = [
            'users' => User::query()->count(),
            'students' => User::query()->role('student')->count(),
            'instructors' => User::query()->role('instructor')->count(),
            'courses' => Course::query()->count(),
            'pending' => Course::query()->where('status', CourseStatus::PendingReview)->count(),
            'revenue' => (int) Order::query()->where('status', OrderStatus::Paid)->sum('total'),
            'newThisMonth' => User::query()->where('created_at', '>=', now()->startOfMonth())->count(),
        ];

        $months = collect(range(5, 0))->map(function (int $offset) {
            $date = now()->startOfMonth()->subMonths($offset);

            return [
                'label' => $date->translatedFormat('M Y'),
                'value' => (int) Order::query()
                    ->where('status', OrderStatus::Paid)
                    ->whereBetween('paid_at', [$date->copy()->startOfMonth(), $date->copy()->endOfMonth()])
                    ->sum('total'),
            ];
        });

        $revenueChart = [
            'type' => 'bar',
            'data' => [
                'labels' => $months->pluck('label'),
                'datasets' => [[
                    'label' => 'Pendapatan',
                    'data' => $months->pluck('value'),
                ]],
            ],
        ];

        $topCourses = Course::query()
            ->published()
            ->with('instructor')
            ->orderByDesc('students_count')
            ->limit(5)
            ->get();

        $recentOrders = Order::query()
            ->with('user')
            ->latest()
            ->limit(6)
            ->get();

        return view('admin.dashboard', compact('stats', 'revenueChart', 'topCourses', 'recentOrders'));
    }
}
