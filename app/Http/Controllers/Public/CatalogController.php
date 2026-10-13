<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Course;
use App\Models\Review;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function index(Request $request): View
    {
        $query = Course::query()
            ->published()
            ->with(['instructor', 'category']);

        $this->applyFilters($query, $request);

        $courses = $query->paginate(12)->withQueryString();

        $categories = Category::query()
            ->active()
            ->root()
            ->with('children')
            ->orderBy('position')
            ->get();

        $filters = $request->only(['q', 'category', 'level', 'price', 'rating', 'sort']);

        // Permintaan htmx hanya mengganti daftar kartu (partial).
        if ($request->header('HX-Request')) {
            return view('public._catalog_results', compact('courses', 'filters'));
        }

        return view('public.catalog', compact('courses', 'categories', 'filters'));
    }

    public function show(Request $request, Course $course): View
    {
        $user = $request->user();

        abort_unless(
            $course->isPublished() || ($user !== null && $user->can('view', $course)),
            404,
        );

        $course->load([
            'instructor',
            'category',
            'sections.lessons',
            'approvedReviews.user',
        ]);

        $related = Course::query()
            ->published()
            ->where('category_id', $course->category_id)
            ->whereKeyNot($course->getKey())
            ->with(['instructor', 'category'])
            ->limit(3)
            ->get();

        $enrollment = $user?->enrollmentFor($course);

        $reviewable = $user !== null && $user->can('create', [Review::class, $course]);

        return view('public.course', compact('course', 'related', 'enrollment', 'reviewable'));
    }

    /**
     * @param  Builder<Course>  $query
     */
    private function applyFilters(Builder $query, Request $request): void
    {
        $query->search($request->string('q')->toString() ?: null);

        if ($request->filled('category')) {
            $category = Category::query()->where('slug', $request->string('category'))->first();

            if ($category !== null) {
                $ids = $category->children()->pluck('id')->push($category->getKey());
                $query->whereIn('category_id', $ids);
            }
        }

        if ($request->filled('level')) {
            $query->where('level', $request->string('level'));
        }

        if ($request->filled('price')) {
            match ($request->string('price')->toString()) {
                'free' => $query->where('price', 0),
                'paid' => $query->where('price', '>', 0),
                default => null,
            };
        }

        if ($request->filled('rating')) {
            $query->where('rating_avg', '>=', (float) $request->input('rating'));
        }

        match ($request->string('sort')->toString()) {
            'popular' => $query->orderByDesc('students_count'),
            'price_low' => $query->orderBy('price'),
            'price_high' => $query->orderByDesc('price'),
            'rating' => $query->orderByDesc('rating_avg'),
            default => $query->latest('published_at'),
        };
    }
}
