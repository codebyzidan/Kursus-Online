<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(Request $request): View
    {
        $courseIds = $request->user()->courses()->pluck('id');

        $enrollments = Enrollment::query()
            ->whereIn('course_id', $courseIds)
            ->with(['user', 'course'])
            ->when($request->filled('q'), function ($query) use ($request): void {
                $term = $request->string('q')->toString();
                $query->whereHas('user', fn ($sub) => $sub->where('name', 'like', "%{$term}%")->orWhere('email', 'like', "%{$term}%"));
            })
            ->orderByDesc('enrolled_at')
            ->paginate(15)
            ->withQueryString();

        return view('instructor.students.index', compact('enrollments'));
    }
}
