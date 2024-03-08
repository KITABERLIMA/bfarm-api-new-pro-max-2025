<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProductRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'images' => 'required|array|max:10', // Pastikan 'images' adalah sebuah array dengan maksimal 10 elemen
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // Setiap elemen harus berupa gambar
        ];
    }

    public function messages()
    {
        return [
            'images.max' => 'You can only upload up to 10 images.',
        ];
    }




    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        $response = response()->json([
            'error' => $validator->errors(),
            'message' => 'Validation errors in your request',
        ], 422); // 422 Unprocessable Entity

        throw new HttpResponseException($response);
    }
}