<?php

namespace App\Http\Controllers\Student;

use App\Actions\Quiz\SubmitQuizAttempt;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuizController extends Controller
{
    public function show(Request $request, Course $course, Quiz $quiz): View
    {
        $this->authorize('learn', $course);

        $quiz->load('questions.options');

        $attemptsUsed = $quiz->attemptsUsedBy($request->user());
        $remaining = $quiz->max_attempts === null ? null : max(0, $quiz->max_attempts - $attemptsUsed);

        return view('student.quizzes.show', compact('course', 'quiz', 'attemptsUsed', 'remaining'));
    }

    public function submit(Request $request, Course $course, Quiz $quiz)
    {
        $this->authorize('learn', $course);

        $validated = $request->validate([
            'answers' => ['array'],
            'answers.*' => ['integer'],
        ]);

        $attempt = app(SubmitQuizAttempt::class)->handle(
            $request->user(),
            $quiz,
            $validated['answers'] ?? [],
        );

        return redirect()
            ->route('student.quizzes.result', [$course, $quiz, $attempt])
            ->with('success', 'Kuis berhasil dikumpulkan.');
    }

    public function result(Request $request, Course $course, Quiz $quiz, QuizAttempt $attempt): View
    {
        $this->authorize('learn', $course);

        abort_unless($attempt->user_id === $request->user()->getKey() && $attempt->quiz_id === $quiz->getKey(), 404);

        $quiz->load('questions.options');
        $answers = is_array($attempt->answers) ? $attempt->answers : [];

        return view('student.quizzes.result', compact('course', 'quiz', 'attempt', 'answers'));
    }
}
