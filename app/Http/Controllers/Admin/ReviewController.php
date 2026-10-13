<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(Request $request): View
    {
        $reviews = Review::query()
            ->with(['user', 'course'])
            ->when($request->filled('status'), function ($query) use ($request): void {
                $query->where('is_approved', $request->string('status')->toString() === 'approved');
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.reviews.index', compact('reviews'));
    }

    public function approve(Review $review): RedirectResponse
    {
        $review->approve();

        return back()->with('success', 'Ulasan disetujui dan rating kursus diperbarui.');
    }

    public function destroy(Review $review): RedirectResponse
    {
        $course = $review->course;
        $review->delete();
        $course?->recalculateRating();

        return back()->with('success', 'Ulasan dihapus.');
    }
}
