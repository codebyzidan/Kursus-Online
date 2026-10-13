<?php

namespace App\Http\Controllers\Instructor;

use App\Actions\Payout\RequestPayout;
use App\Http\Controllers\Controller;
use App\Http\Requests\PayoutRequest;
use App\Models\Payout;
use App\Services\InstructorEarningsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PayoutController extends Controller
{
    public function index(Request $request, InstructorEarningsService $earnings): View
    {
        $instructor = $request->user();

        $payouts = Payout::query()
            ->where('instructor_id', $instructor->getKey())
            ->latest('requested_at')
            ->paginate(10);

        $balance = $earnings->availableBalance($instructor);
        $total = $earnings->totalEarnings($instructor);

        return view('instructor.payouts.index', compact('payouts', 'balance', 'total'));
    }

    public function store(PayoutRequest $request, RequestPayout $action): RedirectResponse
    {
        $action->handle($request->user(), $request->integer('amount'), $request->safe()->except('amount'));

        return back()->with('success', 'Permintaan pencairan dana dikirim.');
    }
}
