<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = Order::query()
            ->with('user')
            ->withCount('items')
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->when($request->filled('q'), function ($query) use ($request): void {
                $term = $request->string('q')->toString();
                $query->where(fn ($sub) => $sub->where('code', 'like', "%{$term}%")
                    ->orWhereHas('user', fn ($user) => $user->where('name', 'like', "%{$term}%")->orWhere('email', 'like', "%{$term}%")));
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        $order->load(['items.course', 'user', 'coupon']);

        return view('admin.orders.show', compact('order'));
    }

    public function refund(Order $order): RedirectResponse
    {
        abort_unless($order->isPaid(), 403, 'Hanya pesanan yang dibayar dapat dikembalikan.');

        $order->forceFill(['status' => OrderStatus::Refunded])->save();

        return back()->with('success', 'Pesanan ditandai sebagai dana dikembalikan.');
    }
}
