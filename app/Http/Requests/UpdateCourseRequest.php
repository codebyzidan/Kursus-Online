<?php

namespace App\Http\Requests;

use App\Enums\CourseLevel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCourseRequest extends FormRequest
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
            'subtitle' => ['nullable', 'string', 'max:200'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'description' => ['nullable', 'string', 'max:20000'],
            'level' => ['required', Rule::in(CourseLevel::values())],
            'price' => ['required', 'integer', 'min:0'],
            'sale_price' => ['nullable', 'integer', 'min:0', 'lt:price'],
            'promo_video_url' => ['nullable', 'url', 'max:255'],
            'outcomes' => ['nullable', 'array'],
            'outcomes.*' => ['string', 'max:200'],
            'requirements' => ['nullable', 'array'],
            'requirements.*' => ['string', 'max:200'],
            'meta_title' => ['nullable', 'string', 'max:150'],
            'meta_description' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'sale_price.lt' => 'Harga diskon harus lebih rendah dari harga normal.',
        ];
    }
}
