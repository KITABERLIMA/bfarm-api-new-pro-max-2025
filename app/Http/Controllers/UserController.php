<?php

namespace App\Http\Controllers;

use App\Models\user;
use App\Models\Address;
use App\Models\user_image;
use App\Models\User_company;
use App\Models\user_individual;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use function Laravel\Prompts\error;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\IndividualRegister;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\IndividualRegisterResource;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserController extends Controller
{
    /**
     * Registers a new individual user.
     */
    public function registerIndividual(IndividualRegister $request): JsonResponse
    {
        $validatedData = $request->validated();

        if (user_individual::where('email', $validatedData['email'])->count() == 1) {
            throw new HttpResponseException(response([
                "error" => [
                    "Email already in use"
                ]
            ], 409));
        }
        DB::beginTransaction();

        try {
            // // Handle the user's image file
            // $image = $request->file('image_file');
            // $imageName = time() . '.' . $image->getClientOriginalExtension();
            // $image->move(public_path('images'), $imageName);

            $user = user::create([
                'token' => null,
                'role_id' => 0,
                'user_type' => $validatedData['user_type'],
                'subs_status' => 0,
            ]);

            // Create Address
            $address = Address::create([
                'village_id' => $validatedData['village_id'],
                'sub_district_id' => $validatedData['sub_district_id'],
                'city_district_id' => $validatedData['city_district_id'],
                'province_id' => $validatedData['province_id'],
                'postal_code' => $validatedData['postal_code'],
            ]);

            $userIndividual = user_individual::create([
                'full_address' => $validatedData['full_address'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
                'user_id' => $user->id,
                'address_id' => $address->id,
                'first_name' => $validatedData['first_name'],
                'last_name' => $validatedData['last_name'],
                'phone' => $validatedData['phone'],
            ]);

            // dd($userIndividual);




            // // Create UserImage
            // $userImage = user_image::create([
            //     'user_id' => $user->id,
            //     'file_name' => $imageName,
            // ]);

            DB::commit();

            return new JsonResponse([
                'success' => true,
                'data' => [
                    'email' => $userIndividual->email,
                ],
                'message' => 'User registered successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            throw new HttpResponseException(response(
                [
                    'error' => 'An unexpected error occurred',
                    'Message' => $e->getMessage()
                ],
                500
            ));
        }
    }

    // public function register(IndividualRegister $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'first_name' => 'required|string|max:255',
    //         'last_name' => 'required|string|max:255',
    //         'position' => 'required|string|max:255',
    //         'company_name' => 'required|string|max:255',
    //         'company_email' => 'required|string|email|max:255|unique:user_company,company_email',
    //         'company_password' => 'required|string|min:8',
    //         'company_phone' => 'required|string|max:20',
    //         'user_type' => 'required|string|in:company',
    //         'full_address' => 'required|string',
    //         'village_id' => 'required|integer',
    //         'sub_district_id' => 'required|integer',
    //         'city_district_id' => 'required|integer',
    //         'province_id' => 'required|integer',
    //         'postal_code' => 'required|integer',
    //         'image_file' => 'required|string', // Assuming handling file as a string for simplicity
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['error' => $validator->errors()], 400);
    //     }

    //     DB::beginTransaction();

    //     try {
    //         $user = User::create([
    //             'role_id' => 2, // Assuming role_id for company is 2
    //             'user_type' => $request->user_type,
    //             'subs_status' => 'active', // Assuming default subs_status is 'active'
    //             'token' => '', // Assuming token generation or handling elsewhere
    //         ]);

    //         $address = Address::create([
    //             // Assuming address table has these columns based on provided address details
    //             'full_address' => $request->full_address,
    //             'village_id' => $request->village_id,
    //             'sub_district_id' => $request->sub_district_id,
    //             'city_district_id' => $request->city_district_id,
    //             'province_id' => $request->province_id,
    //             'postal_code' => $request->postal_code,
    //         ]);

    //         $userCompany = User_company::create([
    //             'user_id' => $user->id,
    //             'address_id' => $address->id,
    //             'first_name' => $request->first_name,
    //             'last_name' => $request->last_name,
    //             'position' => $request->position,
    //             'company_name' => $request->company_name,
    //             'company_email' => $request->company_email,
    //             'company_password' => Hash::make($request->company_password),
    //             'company_phone' => $request->company_phone,
    //         ]);

    //         // Assuming handling file upload or image processing elsewhere
    //         user_image::create([
    //             'user_id' => $user->id,
    //             'file_name' => $request->image_file,
    //             // Other fields like path or URL might be included depending on image handling strategy
    //         ]);

    //         DB::commit();

    //         return response()->json([
    //             'id' => $user->id,
    //             'company_email' => $userCompany->company_email,
    //         ], 200);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return response()->json(['error' => 'An unexpected error occurred'], 500);
    //     }
    // }
}
