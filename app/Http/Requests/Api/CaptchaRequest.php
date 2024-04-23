<?php

namespace App\Http\Requests\Api;


class CaptchaRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'phone'=>['required','phone:CN,mobile','unique:users']
        ];
    }
}
