<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Exports\OrdersExport;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        [$from, $to] = $this->range($request);

        $orders = Order::query()
            ->where('status', OrderStatus::Paid)
            ->whereBetween('paid_at', [$from, $to]);

        $summary = [
            'orders' => (clone $orders)->count(),
            'revenue' => (int) (clone $orders)->sum('total'),
            'average' => (int) round((clone $orders)->avg('total') ?? 0),
        ];

        $topCourses = Course::query()
            ->published()
            ->with('instructor')
            ->orderByDesc('students_count')
            ->limit(8)
            ->get();

        $daily = Order::query()
            ->where('status', OrderStatus::Paid)
            ->whereBetween('paid_at', [$from, $to])
            ->selectRaw('DATE(paid_at) as day, SUM(total) as total')
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        $chart = [
            'type' => 'line',
            'data' => [
                'labels' => $daily->pluck('day'),
                'datasets' => [[
                    'label' => 'Pendapatan harian',
                    'data' => $daily->pluck('total'),
                    'fill' => false,
                ]],
            ],
        ];

        return view('admin.reports.index', compact('summary', 'topCourses', 'chart', 'from', 'to'));
    }

    public function export(Request $request): BinaryFileResponse
    {
        [$from, $to] = $this->range($request);

        return Excel::download(
            new OrdersExport($from, $to),
            'laporan-penjualan-'.$from->format('Ymd').'-'.$to->format('Ymd').'.xlsx',
        );
    }

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    private function range(Request $request): array
    {
        $from = Carbon::parse($request->input('from', now()->startOfMonth()->toDateString()))->startOfDay();
        $to = Carbon::parse($request->input('to', now()->toDateString()))->endOfDay();

        return [$from, $to];
    }
}
