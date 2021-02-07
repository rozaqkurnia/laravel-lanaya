<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
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
        $rules = [
            'name' => 'required|unique:categories,name|max:100'
        ];

        if ($this->getMethod() == 'PUT' || $this->getMethod() == 'PATCH') {
            $rules['name'] = [
                'required',
                'max:100',
                Rule::unique('categories', 'name')->ignore($this->category)
            ];
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'name.required' => 'the category name is required',
            'name.unique' => 'the category name is already taken'
        ];
    }

    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
