<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CouponRequest;
use App\Models\Coupon;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CouponController extends Controller
{
    public function index(): View
    {
        $coupons = Coupon::query()
            ->with('course')
            ->latest()
            ->paginate(15);

        return view('admin.coupons.index', compact('coupons'));
    }

    public function store(CouponRequest $request): RedirectResponse
    {
        Coupon::create($request->validated() + ['used' => 0]);

        return back()->with('success', 'Kupon dibuat.');
    }

    public function update(CouponRequest $request, Coupon $coupon): RedirectResponse
    {
        $coupon->update($request->validated());

        return back()->with('success', 'Kupon diperbarui.');
    }

    public function destroy(Coupon $coupon): RedirectResponse
    {
        $coupon->delete();

        return back()->with('success', 'Kupon dihapus.');
    }
}
