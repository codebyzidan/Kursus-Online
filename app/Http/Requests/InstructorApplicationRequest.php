<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InstructorApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && ! $this->user()->isInstructor();
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'headline' => ['nullable', 'string', 'max:120'],
            'motivation' => ['required', 'string', 'min:30', 'max:2000'],
            'expertise' => ['nullable', 'string', 'max:255'],
            'portfolio_url' => ['nullable', 'url', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'motivation.required' => 'Ceritakan alasan Anda ingin menjadi instruktur.',
            'motivation.min' => 'Mohon isi minimal :min karakter agar admin dapat menilai.',
        ];
    }
}
