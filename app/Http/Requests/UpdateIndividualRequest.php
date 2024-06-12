<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateIndividualRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   */
  public function authorize(): bool
  {
    return false;
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
   */
  public function rules(): array
  {
    return [
      'email' => 'sometimes|email|unique:users,email,' . $this->user()->id,
      'password' => 'sometimes|min:8|confirmed',
      'village' => 'sometimes|string',
      'sub_district' => 'sometimes|string',
      'city_district' => 'sometimes|string',
      'province' => 'sometimes|string',
      'postal_code' => 'sometimes|string',
      'full_address' => 'sometimes|string',
      'first_name' => 'sometimes|string',
      'last_name' => 'sometimes|string',
      'phone' => 'sometimes|string',
      'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
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