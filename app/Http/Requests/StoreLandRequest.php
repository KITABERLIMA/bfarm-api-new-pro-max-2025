<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLandRequest extends FormRequest
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
            'land.user_id' => 'required|exists:users,id',
            'land.land_status' => 'required|string|max:255',
            'land.land_description' => 'nullable|string',
            'land.ownership_status' => 'required|in:owned,rented',
            'land.location' => 'required|string|max:255',
            'land.land_area' => 'required|numeric',
            'address.full_address' => 'required|string|max:255',
            'address.village' => 'required|string|max:255',
            'address.sub_district' => 'required|string|max:255',
            'address.city_district' => 'required|string|max:255',
            'address.province' => 'required|string|max:255',
            'address.postal_code' => 'required|string|max:255',
        ];
    }
}
