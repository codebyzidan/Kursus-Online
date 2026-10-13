<?php

namespace App\Actions\Quiz;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\ValidationException;

class SubmitQuizAttempt
{
    /**
     * Nilai jawaban kuis dan simpan percobaan.
     *
     * @param  array<int|string, int>  $answers  [question_id => option_id]
     *
     * @throws AuthorizationException|ValidationException
     */
    public function handle(User $student, Quiz $quiz, array $answers): QuizAttempt
    {
        if ($quiz->course_id !== null && ! $student->hasEnrolled($quiz->course)) {
            throw new AuthorizationException('Anda belum terdaftar pada kursus ini.');
        }

        if (! $quiz->hasAttemptsRemainingFor($student)) {
            throw ValidationException::withMessages([
                'quiz' => 'Batas percobaan untuk kuis ini sudah habis.',
            ]);
        }

        $quiz->load('questions.options');

        $totalPoints = (int) $quiz->questions->sum('points');
        $earned = 0;

        foreach ($quiz->questions as $question) {
            $selectedOptionId = $answers[$question->getKey()] ?? $answers[(string) $question->getKey()] ?? null;
            $correctOption = $question->options->firstWhere('is_correct', true);

            if ($correctOption !== null && (int) $selectedOptionId === $correctOption->getKey()) {
                $earned += (int) $question->points;
            }
        }

        $score = $totalPoints > 0 ? (int) round($earned / $totalPoints * 100) : 0;

        return QuizAttempt::create([
            'user_id' => $student->getKey(),
            'quiz_id' => $quiz->getKey(),
            'score' => $score,
            'passed' => $score >= $quiz->passing_grade,
            'answers' => $answers,
            'started_at' => now(),
            'finished_at' => now(),
        ]);
    }
}
