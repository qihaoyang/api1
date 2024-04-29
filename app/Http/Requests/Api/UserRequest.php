<?php

namespace App\Http\Requests\Api;


class UserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        switch ($this->method()) {
            case 'POST':
                $rules = [
                    'name'              => [
                        'required', 'between:3,25', 'regex:/^[A-Za-z0-9\-\_]+$/', 'unique:users,name'
                    ],
                    'password'          => ['required', 'alpha_dash', 'min:6'],
                    'verification_key'  => ['required', 'string'],
                    'verification_code' => ['required', 'string'],
                ];
                break;
            case 'PATCH':
                $user_id = auth('api')->id();
                $rules   = [
                    'name'            => ['between:3,25', 'regex:/^[A-Za-z0-9\-\_]+$/', 'unique:users,name,'.$user_id],
                    'email'           => ['email', 'unique:users,email,'.$user_id],
                    'introduction'    => ['max:80'],
                    'avatar_image_id' => ['exists:images,id,type,avatar,user_id,'.$user_id],
                ];
                break;
        }
        return $rules;
    }

    public function attributes()
    {
        return [
            'verification_key'  => '短信验证码 key',
            'verification_code' => '短信验证码',
        ];
    }

    public function messages()
    {
        return [
            'name.unique'   => '用户名已被占用，请重新填写',
            'name.regex'    => '用户名只支持英文、数字、横杆和下划线。',
            'name.between'  => '用户名必须介于 3 - 25 个字符之间。',
            'name.required' => '用户名不能为空。',
        ];
    }
}
