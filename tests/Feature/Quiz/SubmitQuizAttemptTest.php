<?php

use App\Actions\Quiz\SubmitQuizAttempt;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Option;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Validation\ValidationException;

beforeEach(function () {
    $this->student = User::factory()->student()->create();
    $this->course = Course::factory()->published()->create();
    Enrollment::factory()->create([
        'user_id' => $this->student->getKey(),
        'course_id' => $this->course->getKey(),
    ]);

    $this->quiz = Quiz::factory()->create([
        'course_id' => $this->course->getKey(),
        'passing_grade' => 70,
        'max_attempts' => 2,
    ]);

    $this->questions = collect(range(1, 2))->map(function (int $i) {
        $question = Question::create([
            'quiz_id' => $this->quiz->getKey(),
            'text' => 'Pertanyaan '.$i,
            'points' => 1,
            'position' => $i,
        ]);

        $correct = Option::create([
            'question_id' => $question->getKey(),
            'text' => 'Benar',
            'is_correct' => true,
            'position' => 1,
        ]);

        Option::create([
            'question_id' => $question->getKey(),
            'text' => 'Salah',
            'is_correct' => false,
            'position' => 2,
        ]);

        return ['question' => $question, 'correct' => $correct];
    });
});

it('memberi nilai penuh saat semua jawaban benar', function () {
    $answers = $this->questions->mapWithKeys(
        fn (array $q) => [$q['question']->getKey() => $q['correct']->getKey()],
    )->all();

    $attempt = app(SubmitQuizAttempt::class)->handle($this->student, $this->quiz, $answers);

    expect($attempt->score)->toBe(100)
        ->and($attempt->passed)->toBeTrue();
});

it('memberi nilai nol saat semua jawaban salah', function () {
    $answers = $this->questions->map(
        fn (array $q) => $q['question']->getKey(),
    )->mapWithKeys(fn (int $id) => [$id => 99999])->all();

    $attempt = app(SubmitQuizAttempt::class)->handle($this->student, $this->quiz, $answers);

    expect($attempt->score)->toBe(0)
        ->and($attempt->passed)->toBeFalse();
});

it('menolak percobaan melebihi batas', function () {
    $answers = [];

    app(SubmitQuizAttempt::class)->handle($this->student, $this->quiz, $answers);
    app(SubmitQuizAttempt::class)->handle($this->student, $this->quiz, $answers);

    app(SubmitQuizAttempt::class)->handle($this->student, $this->quiz, $answers);
})->throws(ValidationException::class);
