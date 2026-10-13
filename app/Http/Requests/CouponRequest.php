<?php

namespace App\Http\Requests;

use App\Enums\CouponType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->can('coupons.manage');
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $couponId = $this->route('coupon')?->getKey();

        return [
            'code' => ['required', 'string', 'max:40', Rule::unique('coupons', 'code')->ignore($couponId)],
            'description' => ['nullable', 'string', 'max:200'],
            'type' => ['required', Rule::in(CouponType::values())],
            'value' => ['required', 'integer', 'min:1'],
            'max_discount' => ['nullable', 'integer', 'min:1'],
            'min_purchase' => ['nullable', 'integer', 'min:0'],
            'course_id' => ['nullable', 'integer', 'exists:courses,id'],
            'quota' => ['nullable', 'integer', 'min:1'],
            'valid_from' => ['nullable', 'date'],
            'valid_until' => ['nullable', 'date', 'after_or_equal:valid_from'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'code' => strtoupper((string) $this->input('code')),
            'is_active' => $this->boolean('is_active', true),
            'min_purchase' => $this->input('min_purchase', 0),
        ]);
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'valid_until.after_or_equal' => 'Tanggal berakhir harus setelah tanggal mulai.',
        ];
    }
}
