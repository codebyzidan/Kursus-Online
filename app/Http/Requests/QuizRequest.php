<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuizRequest extends FormRequest
{
    public function authorize(): bool
    {
        $course = $this->route('course');

        if ($course !== null) {
            return (bool) $this->user()?->can('update', $course);
        }

        return $this->user()?->isInstructor() || $this->user()?->isAdmin();
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'course_id' => ['nullable', 'integer', 'exists:courses,id'],
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:2000'],
            'passing_grade' => ['required', 'integer', 'between:0,100'],
            'max_attempts' => ['nullable', 'integer', 'min:1', 'max:100'],
            'duration_minutes' => ['nullable', 'integer', 'min:1', 'max:600'],
            'is_final' => ['sometimes', 'boolean'],
            'is_published' => ['sometimes', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_final' => $this->boolean('is_final'),
            'is_published' => $this->boolean('is_published', true),
        ]);
    }
}
