<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Address;
use App\Models\user_image;
use Illuminate\Support\Str;
use App\Models\User_company;
use Illuminate\Http\Request;
use App\Models\User_individual;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\CompanyRegisterRequest;
use App\Http\Requests\IndividualRegisterRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserController extends Controller
{
    /**
     * Registers a new individual user.
     */
    public function registerIndividual(IndividualRegisterRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        if (User::where('email', $validatedData['email'])->count() == 1) {
            throw new HttpResponseException(response([
                "error" => [
                    "Email already in use"
                ]
            ], 409));
        }

        DB::beginTransaction();

        try {
            $imgname = time() . '.' . Str::random(32) . "." . $request->image->getClientOriginalExtension();
            Storage::disk('public')->put($imgname, file_get_contents($request->image));

            $user = user::create([
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
                'user_type' => $validatedData['user_type'],
                'subs_status' => 0,
            ]);

            $address = Address::create([
                'village' => $validatedData['village'],
                'sub_district' => $validatedData['sub_district'],
                'city_district' => $validatedData['city_district'],
                'province' => $validatedData['province'],
                'postal_code' => $validatedData['postal_code'],
                'full_address' => $validatedData['full_address'],
            ]);

            $userIndividual = User_individual::create([
                'user_id' => $user->id,
                'address_id' => $address->id,
                'first_name' => $validatedData['first_name'],
                'last_name' => $validatedData['last_name'],
                'phone' => $validatedData['phone'],
            ]);

            // Create UserImage
            $userImage = user_image::create([
                'user_id' => $user->id,
                'image' => $imgname,
            ]);

            DB::commit();

            return new JsonResponse([
                'success' => true,
                'data' => [
                    'email' => $user->email,
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
    /**
     * Registers a new individual user.
     */
    public function registerCompany(CompanyRegisterRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        if (User::where('email', $validatedData['email'])->count() == 1) {
            throw new HttpResponseException(response([
                "error" => [
                    "Email already in use"
                ]
            ], 409));
        }

        DB::beginTransaction();

        try {
            $imgname = time() . '.' . Str::random(32) . "." . $request->image->getClientOriginalExtension();
            Storage::disk('public')->put($imgname, file_get_contents($request->image));

            $user = user::create([
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
                'user_type' => $validatedData['user_type'],
                'subs_status' => 0,
            ]);

            $address = Address::create([
                'full_address' => $validatedData['full_address'],
                'village' => $validatedData['village'],
                'sub_district' => $validatedData['sub_district'],
                'city_district' => $validatedData['city_district'],
                'province' => $validatedData['province'],
                'postal_code' => $validatedData['postal_code'],
            ]);

            $userCompany = User_company::create([
                'address_id' => $address->id,
                'user_id' => $user->id,
                'first_name' => $validatedData['first_name'],
                'last_name' => $validatedData['last_name'],
                'position' => $validatedData['position'],
                'company_name' => $validatedData['company_name'],
                'company_phone' => $validatedData['company_phone'],
            ]);

            // Create UserImage
            $userImage = user_image::create([
                'user_id' => $user->id,
                'image' => $imgname,
            ]);

            DB::commit();

            return new JsonResponse([
                'success' => true,
                'data' => [
                    'email' => $user->email,
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

    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        $userData = [
            'id' => $user->id,
            'role_id' => $user->role_id,
            'email' => $user->email,
            'user_type' => $user->user_type,
            'subs_status' => $user->subs_status,
        ];

        $token = $user->createToken($credentials['email'])->plainTextToken;

        return response()->json([
            'success' => true,
            'token' => $token,
            'message' => 'User logged in successfully',
        ]);
    }

    public function getUser($id): JsonResponse
    {
        // Mengambil user berdasarkan ID
        $user = User::find($id);

        // Memeriksa apakah user ditemukan
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        // Memuat user_image dan address yang berkaitan dengan user
        $userImage = user_image::where('user_id', $user->id)->first();
        $address = Address::where('id', function ($query) use ($user) {
            if ($user->user_type == 'individual') {
                return $query->select('address_id')->from('User_individuals')->where('user_id', $user->id);
            } else {
                return $query->select('address_id')->from('user_companies')->where('user_id', $user->id);
            }
        })->first();

        // Memuat data tambahan berdasarkan tipe user
        if ($user->user_type == 'individual') {
            $additionalData = User_individual::where('user_id', $user->id)->first();
        } else {
            $additionalData = User_company::where('user_id', $user->id)->first();
        }

        // Membangun response
        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
                'user_additional_data' => $additionalData, // Ini adalah User_individual atau user_company
                'address' => $address,
                'user_image' => $userImage,
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        if ($user) {
            $user->currentAccessToken()->delete();
            return response()->json(['message' => 'You have been successfully logged out.'], 200);
        }

        return response()->json(['message' => 'Unauthenticated.'], 401);
    }
}
