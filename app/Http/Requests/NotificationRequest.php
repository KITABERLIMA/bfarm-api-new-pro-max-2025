<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class NotificationRequest extends FormRequest
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
      'user_id' => 'required',
      'notif_type' => 'required|exists:table_name,column_name',
      'title' => 'required',
      'message' => 'nullable',
      'status' => 'required'
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
