<?php

namespace App\Http\Requests\Api;


class ReplyRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'content' => 'required|min:2'
        ];
    }
}
