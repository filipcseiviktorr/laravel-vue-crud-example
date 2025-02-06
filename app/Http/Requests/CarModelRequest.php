<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CarModelRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:40',
                Rule::unique('car_models', 'name')->ignore($this->route('car_model')),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Model name is required.',
            'name.string' => 'Model name must be a string.',
            'name.max' => 'Model name must not be greater than 40 characters.',
            'name.unique' => 'Model name must be unique.',
            'image.image' => 'The file must be an image.',
            'image.mimes' => 'The image must be a file of type: jpeg, png, jpg.',
            'image.max' => 'The image must not be greater than 2048 kilobytes.',
        ];
    }
}
