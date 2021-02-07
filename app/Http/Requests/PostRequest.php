<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class PostRequest extends FormRequest
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
            'category_id'   => 'required|exists:categories,id',
            'title'         => 'required|unique:posts,title|max:100',
            'body'          => 'required',
            'published'     => 'required'
        ];

        if ($this->getMethod() == 'PUT' || $this->getMethod() == 'PATCH') {
            $rules['title'] = [
                'required',
                'max:100',
                Rule::unique('posts', 'title')->ignore($this->post)
            ];
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'category_id.required' => 'the post category is required',
            'category_id.exists' => 'the selected post category is not available',
            'title.required' => 'the post title is required',
            'title.unique' => 'the post title is already taken',
            'title.max' => 'the post title cannot be more than 100 characters.',
            'body.required' => 'the post body is required',
            'published.required' => 'the post publish status is required'
        ];
    }

    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}