<?php

namespace App\Http\Controllers\Student;

use App\Actions\Checkout\PlaceOrder;
use App\Events\OrderPaid;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCheckoutRequest;
use App\Models\Course;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function create(Request $request): View
    {
        $ids = array_filter((array) $request->input('courses', []));

        $courses = Course::query()
            ->published()
            ->whereIn('id', $ids)
            ->with(['instructor', 'category'])
            ->get();

        $subtotal = (int) $courses->sum(fn (Course $course) => $course->getEffectivePriceAttribute());

        return view('checkout.create', compact('courses', 'subtotal'));
    }

    public function store(StoreCheckoutRequest $request, PlaceOrder $placeOrder): RedirectResponse
    {
        $order = $placeOrder->handle(
            $request->user(),
            $request->validated('courses'),
            $request->validated('coupon'),
        );

        // Kursus gratis: langsung aktifkan akses tanpa gateway.
        if ($order->total === 0) {
            DB::transaction(function () use ($order): void {
                $order->markPaid('free');
                OrderPaid::dispatch($order->fresh());
            });

            return redirect()
                ->route('checkout.success', $order)
                ->with('success', 'Kursus gratis berhasil diikuti.');
        }

        return redirect()->route('checkout.show', $order);
    }

    public function show(Request $request, Order $order): View
    {
        $this->authorize('view', $order);

        $order->load(['items.course', 'coupon']);

        return view('checkout.show', compact('order'));
    }

    public function success(Request $request, Order $order): View
    {
        $this->authorize('view', $order);

        $order->load('items.course');

        return view('checkout.success', compact('order'));
    }
}
