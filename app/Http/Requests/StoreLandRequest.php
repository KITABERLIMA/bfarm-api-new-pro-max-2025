<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreLandRequest extends FormRequest
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
      'land_status' => 'nullable|in:mapped,unmapped',
      'land_description' => 'nullable|string',
      'ownership_status' => 'nullable|in:owned,rented',
      'location' => 'required|string|max:255',
      'land_area' => 'required|numeric',
      'full_address' => 'required|string|max:255',
      'village' => 'required|string|max:255',
      'sub_district' => 'required|string|max:255',
      'city_district' => 'required|string|max:255',
      'province' => 'required|string|max:255',
      'postal_code' => 'required|string|max:255',
      'image' => 'required|image|mimes:jpeg,png,jpg|max:2048'
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
