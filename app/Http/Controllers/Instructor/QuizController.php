<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Http\Requests\QuestionRequest;
use App\Http\Requests\QuizRequest;
use App\Models\Course;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class QuizController extends Controller
{
    public function index(Request $request): View
    {
        $courseIds = $request->user()->courses()->pluck('id');

        $quizzes = Quiz::query()
            ->whereIn('course_id', $courseIds)
            ->with('course')
            ->withCount(['questions', 'attempts'])
            ->latest()
            ->paginate(10);

        return view('instructor.quizzes.index', compact('quizzes'));
    }

    public function create(Request $request): View
    {
        $courses = $request->user()->courses()->orderBy('title')->get();

        return view('instructor.quizzes.create', compact('courses'));
    }

    public function store(QuizRequest $request): RedirectResponse
    {
        $course = Course::query()->findOrFail($request->validated('course_id'));
        $this->authorize('update', $course);

        $quiz = $course->quizzes()->create($request->safe()->except('course_id'));

        return redirect()
            ->route('instructor.quizzes.edit', [$course, $quiz])
            ->with('success', 'Kuis dibuat. Tambahkan pertanyaan di bawah.');
    }

    public function edit(Course $course, Quiz $quiz): View
    {
        $this->authorize('update', $course);
        $this->ensureQuizBelongsToCourse($course, $quiz);

        $quiz->load(['questions.options']);

        return view('instructor.quizzes.edit', compact('course', 'quiz'));
    }

    public function update(QuizRequest $request, Course $course, Quiz $quiz): RedirectResponse
    {
        $this->authorize('update', $course);
        $this->ensureQuizBelongsToCourse($course, $quiz);

        $quiz->update($request->safe()->except('course_id'));

        return back()->with('success', 'Kuis diperbarui.');
    }

    public function destroy(Course $course, Quiz $quiz): RedirectResponse
    {
        $this->authorize('update', $course);
        $this->ensureQuizBelongsToCourse($course, $quiz);

        $quiz->delete();

        return redirect()->route('instructor.quizzes.index')->with('success', 'Kuis dihapus.');
    }

    public function storeQuestion(QuestionRequest $request, Course $course, Quiz $quiz): RedirectResponse
    {
        $this->authorize('update', $course);
        $this->ensureQuizBelongsToCourse($course, $quiz);

        $this->saveQuestion($quiz, $request);

        return back()->with('success', 'Pertanyaan ditambahkan.');
    }

    public function updateQuestion(QuestionRequest $request, Course $course, Quiz $quiz, Question $question): RedirectResponse
    {
        $this->authorize('update', $course);
        $this->ensureQuizBelongsToCourse($course, $quiz);
        abort_unless($question->quiz_id === $quiz->getKey(), 404);

        DB::transaction(function () use ($question, $request): void {
            $question->options()->delete();
            $this->saveQuestion($question->quiz, $request, $question);
        });

        return back()->with('success', 'Pertanyaan diperbarui.');
    }

    public function destroyQuestion(Course $course, Quiz $quiz, Question $question): RedirectResponse
    {
        $this->authorize('update', $course);
        $this->ensureQuizBelongsToCourse($course, $quiz);
        abort_unless($question->quiz_id === $quiz->getKey(), 404);

        $question->delete();

        return back()->with('success', 'Pertanyaan dihapus.');
    }

    private function saveQuestion(Quiz $quiz, QuestionRequest $request, ?Question $question = null): void
    {
        $data = $request->validated();

        $attributes = [
            'text' => $data['text'],
            'explanation' => $data['explanation'] ?? null,
            'points' => $data['points'] ?? 1,
            'position' => $question !== null ? $question->position : ((int) $quiz->questions()->max('position') + 1),
        ];

        if ($question !== null) {
            $question->update($attributes);
        } else {
            $question = $quiz->questions()->create($attributes);
        }

        foreach ($data['options'] as $index => $text) {
            $question->options()->create([
                'text' => $text,
                'is_correct' => (int) $data['correct'] === $index,
                'position' => $index + 1,
            ]);
        }
    }

    private function ensureQuizBelongsToCourse(Course $course, Quiz $quiz): void
    {
        abort_unless($quiz->course_id === $course->getKey(), 404);
    }
}
