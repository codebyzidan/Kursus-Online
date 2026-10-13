<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'courses' => ['required', 'array', 'min:1'],
            'courses.*' => ['integer', 'exists:courses,id'],
            'coupon' => ['nullable', 'string', 'max:50'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'courses.required' => 'Pilih minimal satu kursus untuk dibeli.',
            'courses.*.exists' => 'Kursus yang dipilih tidak ditemukan.',
        ];
    }
}
