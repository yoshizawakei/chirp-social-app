<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "email" => "required|email|exists:users,email",
            "password" => "required|min:8",
        ];
    }

    public function messages()
    {
        return [
            "email.required" => "メールアドレスを入力してください。",
            "email.email" => "有効なメールアドレスを入力してください。",
            "email.exists" => "ログイン情報が登録されていません。",
            "password.required" => "パスワードを入力してください。",
            "password.min" => "パスワードは8文字以上で入力してください。",
            "password.exists" => "ログイン情報が登録されていません。",
        ];
    }
}
