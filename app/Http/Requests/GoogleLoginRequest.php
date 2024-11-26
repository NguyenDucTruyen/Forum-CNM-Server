<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GoogleLoginRequest extends FormRequest
{
    /**
     * Xác định xem người dùng có quyền thực hiện yêu cầu này hay không.
     *
     * @return bool
     */
    public function authorize()
    {
        // Return true để cho phép tất cả người dùng gửi yêu cầu này
        return true;
    }

    /**
     * Lấy các quy tắc xác thực cho yêu cầu này.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'access_token' => 'required|string',  // Kiểm tra access_token có tồn tại và là kiểu chuỗi
        ];
    }

    /**
     * Tùy chỉnh thông báo lỗi.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'access_token.required' => 'The access_token is required.',
            'access_token.string' => 'The access_token must be a string.',
        ];
    }
}
