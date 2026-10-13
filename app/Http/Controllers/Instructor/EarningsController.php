<?php

namespace App\Http\Controllers\Instructor;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\InstructorEarningsService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class EarningsController extends Controller
{
    public function index(Request $request, InstructorEarningsService $earnings): View
    {
        $instructor = $request->user();

        $from = Carbon::parse($request->input('from', now()->startOfMonth()->toDateString()))->startOfDay();
        $to = Carbon::parse($request->input('to', now()->endOfMonth()->toDateString()))->endOfDay();

        $stats = [
            'total' => $earnings->totalEarnings($instructor),
            'period' => $earnings->earningsBetween($instructor, $from, $to),
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
            'type' => 'bar',
            'data' => [
                'labels' => $months->pluck('label'),
                'datasets' => [[
                    'label' => 'Pendapatan',
                    'data' => $months->pluck('value'),
                ]],
            ],
        ];

        $perCourse = $instructor->courses()
            ->withCount('enrollments')
            ->withSum('orderItems as earnings_sum', 'instructor_earning')
            ->orderByDesc('earnings_sum')
            ->get();

        $transactions = Order::query()
            ->where('status', OrderStatus::Paid)
            ->whereHas('items', fn ($query) => $query->where('instructor_id', $instructor->getKey()))
            ->with(['items' => fn ($query) => $query->where('instructor_id', $instructor->getKey()), 'user'])
            ->latest('paid_at')
            ->limit(10)
            ->get();

        return view('instructor.earnings.index', compact('stats', 'chart', 'perCourse', 'transactions', 'from', 'to'));
    }
}
