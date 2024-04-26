<?php

namespace App\Http\Requests\Api;

class AuthorizationRequest extends FormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $rules = [
            'username' => ['required', 'string'],
            'password' => ['required', 'string', 'alpha_dash'],
        ];

        return $rules;
    }
}

