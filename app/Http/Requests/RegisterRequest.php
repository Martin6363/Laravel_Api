<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use \Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterRequest extends FormRequest
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
            'name' => ['required', 'min:2', 'max:60'],
            'email' => ['required', 'unique:users', 'email'],
            'password' => ['required', 'min:8', 'max:16']
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
            // 'name.required' => "Name is required",
        ];
    }
}
