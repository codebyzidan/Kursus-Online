<?php

namespace App\Http\Controllers\Student;

use App\Actions\Review\SubmitReview;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReviewRequest;
use App\Models\Course;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;

class ReviewController extends Controller
{
    public function store(ReviewRequest $request, Course $course, SubmitReview $action): RedirectResponse
    {
        $this->authorize('create', [Review::class, $course]);

        $action->handle(
            $request->user(),
            $course,
            $request->validated('rating'),
            $request->validated('body'),
        );

        return back()->with('success', 'Ulasan terkirim dan menunggu moderasi admin.');
    }
}
