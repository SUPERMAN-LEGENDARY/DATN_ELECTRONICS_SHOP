<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'rating'  => ['required', 'integer', 'min:1', 'max:5'],
            'content' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'rating.required' => 'Vui lòng chọn số sao đánh giá.',
            'rating.min'      => 'Đánh giá thấp nhất là 1 sao.',
            'rating.max'      => 'Đánh giá cao nhất là 5 sao.',
            'content.max'     => 'Nội dung đánh giá không vượt quá 1000 ký tự.',
        ];
    }
}
