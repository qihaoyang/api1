<?php

namespace App\Http\Requests\Api;


class TopicRequest extends FormRequest
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
                    'title'       => 'required|string',
                    'body'        => 'required|string',
                    'category_id' => 'required|exists:categories,id',
                ];
                break;
            case 'PATCH':
                $rules = [
                    'title'       => 'string',
                    'body'        => 'string',
                    'category_id' => 'exists:categories,id',
                ];
                break;

        }
        return $rules;
    }

    public function attributes()
    {
        return [
            'title'       => '标题',
            'body'        => '内容',
            'category_id' => '分类',
        ];
    }
}
