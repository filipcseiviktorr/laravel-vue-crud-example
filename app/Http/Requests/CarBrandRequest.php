<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CarBrandRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:40',
                Rule::unique('car_brands', 'name')->ignore($this->route('car_brand')),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Brand name is required.',
            'name.string' => 'Brand name must be a string.',
            'name.max' => 'Brand name must not be greater than 40 characters.',
            'name.unique' => 'Brand name must be unique.',
        ];
    }
}
