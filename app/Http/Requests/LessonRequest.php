<?php

namespace App\Http\Requests;

use App\Enums\LessonType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LessonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->can('update', $this->route('course'));
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            'type' => ['required', Rule::in(LessonType::values())],
            'content' => ['nullable', 'string', 'max:50000'],
            'video_url' => ['nullable', 'url', 'max:255'],
            'duration' => ['nullable', 'integer', 'min:0', 'max:86400'],
            'is_preview' => ['sometimes', 'boolean'],
            'is_published' => ['sometimes', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_preview' => $this->boolean('is_preview'),
            'is_published' => $this->boolean('is_published', true),
        ]);
    }
}
