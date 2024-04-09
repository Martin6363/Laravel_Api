<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use \Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ImageRequest extends FormRequest
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
            'image' => [
                        'image', 
                        'mimes:jpg,png,jpeg,gif,svg', 
                        'max:2048'
                        ]
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
