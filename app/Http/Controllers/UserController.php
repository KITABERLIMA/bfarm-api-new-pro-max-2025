<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Address;
use App\Models\user_image;
use Illuminate\Support\Str;
use App\Models\user_individual;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use function Laravel\Prompts\error;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\IndividualRegisterRequest;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Storage;

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
            ]);

            $userIndividual = user_individual::create([
                'full_address' => $validatedData['full_address'],
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

        // Here, we're simulating token creation. You should replace this with actual token logic (e.g., JWT).
        $token = Str::random(60);

        // Store or use the token as per your application's requirement.
        // For demonstration, we're just returning it in the response.
        return response()->json([
            'success' => true,
            'token' => $token,
            'message' => 'User logged in successfully',
        ]);
    }
}
