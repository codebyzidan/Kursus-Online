<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = Order::query()
            ->where('user_id', $request->user()->getKey())
            ->withCount('items')
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('student.orders.index', compact('orders'));
    }

    public function show(Request $request, Order $order): View
    {
        $this->authorize('view', $order);

        $order->load(['items.course', 'coupon']);

        return view('student.orders.show', compact('order'));
    }

    public function invoice(Request $request, Order $order, InvoiceService $invoices): Response
    {
        $this->authorize('view', $order);

        return response($invoices->contents($order), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$order->code.'.pdf"',
        ]);
    }
}
