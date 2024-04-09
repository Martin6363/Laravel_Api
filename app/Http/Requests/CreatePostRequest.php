<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use \Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreatePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'max:255'],
            'published' => ['nullable','boolean'],
            'description' => ['required', 'max:1000', 'min:5'],
            'image' => ['image', 'mimes:jpg,jpeg,png,gif,svg', 'max:2048']
        ];
    }

    public function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'error' => true,
            'message' => 'Oops! Some errors occurred',
            'errorsList' => $validator->errors()
        ])); 
    }

    public function messages() {
        return [
            // 'title.required' => "Title"
        ];
    }
}
