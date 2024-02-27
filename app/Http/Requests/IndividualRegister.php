<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndividualRegister extends FormRequest
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
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'user_type' => 'required|in:individual,company',
            'email' => 'required|string|email|max:255|unique:user_individual,email',
            'password' => 'required|string|min:6',
            'full_address' => 'required|string',
            'village_id' => 'required|integer',
            'sub_district_id' => 'required|integer',
            'city_district_id' => 'required|integer',
            'province_id' => 'required|integer',
            'postal_code' => 'required|integer',
            'image_file' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ];
    }
}
