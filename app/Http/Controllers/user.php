<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User_individual;
use App\Models\User_company;
use App\Models\user;
use App\Models\Address;
use App\Models\user_image;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserController extends Controller
{
    /**
     * Registers a new individual user.
     */
    public function registerIndividual(Request $request)
    {
        // Validate incoming request
        $validator = Validator::make($request->all(), [
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
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        DB::beginTransaction();

        try {
            // Handle the user's image file
            if ($request->hasFile('image_file')) {
                $image = $request->file('image_file');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images'), $imageName);
            }

            // Create Address
            $address = Address::create([
                'full_address' => $request->full_address,
                'village_id' => $request->village_id,
                'sub_district_id' => $request->sub_district_id,
                'city_district_id' => $request->city_district_id,
                'province_id' => $request->province_id,
                'postal_code' => $request->postal_code,
            ]);

            // Create User
            $user = User::create([
                'role_id' => 1, // Assuming '1' is the role ID for 'individual'. Adjust as necessary.
                'user_type' => $request->user_type,
                'subs_status' => 'active', // Assuming default subscription status is 'active'
                'token' => '', // Handle token generation as necessary
            ]);

            // Create UserIndividual
            $userIndividual = User_individual::create([
                'address_id' => $address->id,
                'user_id' => $user->id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
            ]);

            // Create UserImage
            $userImage = user_image::create([
                'user_id' => $user->id,
                'file_name' => $imageName,
                // Add any other necessary fields
            ]);

            DB::commit();

            return response()->json(['id' => $userIndividual->id, 'email' => $userIndividual->email], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'An unexpected error occurred'], 500);
        }
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'company_email' => 'required|string|email|max:255|unique:user_company,company_email',
            'company_password' => 'required|string|min:8',
            'company_phone' => 'required|string|max:20',
            'user_type' => 'required|string|in:company',
            'full_address' => 'required|string',
            'village_id' => 'required|integer',
            'sub_district_id' => 'required|integer',
            'city_district_id' => 'required|integer',
            'province_id' => 'required|integer',
            'postal_code' => 'required|integer',
            'image_file' => 'required|string', // Assuming handling file as a string for simplicity
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        DB::beginTransaction();

        try {
            $user = User::create([
                'role_id' => 2, // Assuming role_id for company is 2
                'user_type' => $request->user_type,
                'subs_status' => 'active', // Assuming default subs_status is 'active'
                'token' => '', // Assuming token generation or handling elsewhere
            ]);

            $address = Address::create([
                // Assuming address table has these columns based on provided address details
                'full_address' => $request->full_address,
                'village_id' => $request->village_id,
                'sub_district_id' => $request->sub_district_id,
                'city_district_id' => $request->city_district_id,
                'province_id' => $request->province_id,
                'postal_code' => $request->postal_code,
            ]);

            $userCompany = User_company::create([
                'user_id' => $user->id,
                'address_id' => $address->id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'position' => $request->position,
                'company_name' => $request->company_name,
                'company_email' => $request->company_email,
                'company_password' => Hash::make($request->company_password),
                'company_phone' => $request->company_phone,
            ]);

            // Assuming handling file upload or image processing elsewhere
            user_image::create([
                'user_id' => $user->id,
                'file_name' => $request->image_file,
                // Other fields like path or URL might be included depending on image handling strategy
            ]);

            DB::commit();

            return response()->json([
                'id' => $user->id,
                'company_email' => $userCompany->company_email,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'An unexpected error occurred'], 500);
        }
    }
}
