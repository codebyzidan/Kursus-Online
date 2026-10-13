<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Wishlist;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WishlistController extends Controller
{
    public function index(Request $request): View
    {
        $wishlists = Wishlist::query()
            ->where('user_id', $request->user()->getKey())
            ->with(['course.instructor', 'course.category'])
            ->latest()
            ->paginate(9);

        return view('student.wishlist.index', compact('wishlists'));
    }

    public function store(Request $request, Course $course): RedirectResponse
    {
        Wishlist::query()->firstOrCreate([
            'user_id' => $request->user()->getKey(),
            'course_id' => $course->getKey(),
        ]);

        return back()->with('success', 'Kursus ditambahkan ke wishlist.');
    }

    public function destroy(Request $request, Course $course): RedirectResponse
    {
        Wishlist::query()
            ->where('user_id', $request->user()->getKey())
            ->where('course_id', $course->getKey())
            ->delete();

        return back()->with('success', 'Kursus dihapus dari wishlist.');
    }
}
