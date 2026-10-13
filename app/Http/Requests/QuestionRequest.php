<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuestionRequest extends FormRequest
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
            'text' => ['required', 'string', 'max:1000'],
            'explanation' => ['nullable', 'string', 'max:1000'],
            'points' => ['nullable', 'integer', 'min:1', 'max:100'],
            'options' => ['required', 'array', 'min:2', 'max:6'],
            'options.*' => ['required', 'string', 'max:500'],
            'correct' => ['required', 'integer', 'min:0', 'max:5'],
        ];
    }

    protected function prepareForValidation(): void
    {
        // Hapus opsi kosong sebelum validasi.
        $options = array_values(array_filter((array) $this->input('options', []), fn ($value) => filled($value)));

        $this->merge(['options' => $options]);
    }
}
