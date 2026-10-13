<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Course;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $featured = Course::query()
            ->published()
            ->with(['instructor', 'category'])
            ->orderByDesc('students_count')
            ->limit(6)
            ->get();

        $latest = Course::query()
            ->published()
            ->with(['instructor', 'category'])
            ->latest('published_at')
            ->limit(3)
            ->get();

        $categories = Category::query()
            ->active()
            ->root()
            ->withCount([
                'courses as published_courses_count' => fn ($query) => $query->whereNotNull('published_at'),
            ])
            ->orderBy('position')
            ->limit(8)
            ->get();

        return view('public.home', compact('featured', 'latest', 'categories'));
    }
}
