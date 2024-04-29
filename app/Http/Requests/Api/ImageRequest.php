<?php

namespace App\Http\Requests\Api;


class ImageRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $rules['type'] = ['required', 'string', 'in:topic,avatar'];
        if ($this->type == 'avatar') {
            $rules['image'] = ['required', 'mimes:jpeg,bmp,png,gif,webp', 'dimensions:min_width=200,min_height=200'];
        } else {
            $rules['image'] = ['required', 'mimes:jpeg,bmp,png,gif,webp'];
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'image.dimensions' => '图片的清晰度不够，宽和高需要 200px 以上',
        ];
    }
}
