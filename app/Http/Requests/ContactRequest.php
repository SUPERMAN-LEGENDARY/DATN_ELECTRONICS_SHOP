<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
{
    /**
     * Xác thực quyền
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Rule validate
     */
    public function rules(): array
    {
        return [

            'name' => [
                'required',
                'string',
                'min:3',
                'max:100'
            ],

            'email' => [
                'required',
                'email',
                'max:100'
            ],

            'phone' => [
                'required',
                'regex:/^[0-9]{9,11}$/'
            ],

            'subject' => [
                'required',
                'string',
                'max:255'
            ],

            'message' => [
                'required',
                'string',
                'min:10'
            ]

        ];
    }

    /**
     * Thông báo lỗi
     */
    public function messages(): array
    {
        return [

            'name.required' => 'Vui lòng nhập họ tên.',
            'name.min' => 'Họ tên tối thiểu 3 ký tự.',

            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không hợp lệ.',

            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'phone.regex' => 'Số điện thoại không hợp lệ.',

            'subject.required' => 'Vui lòng chọn chủ đề.',

            'message.required' => 'Vui lòng nhập nội dung.',
            'message.min' => 'Nội dung phải từ 10 ký tự.'

        ];
    }
}
