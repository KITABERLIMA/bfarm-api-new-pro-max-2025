<?php

namespace App\Http\Controllers\backend;

use App\Models\User;
use App\Models\Address;
use App\Models\otp_code;
use App\Mail\SendOtpMail;
use App\Models\user_image;
use Illuminate\Support\Str;
use App\Models\User_company;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\user_individual;
use App\Models\user_notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\user_admin_notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\CompanyRegisterRequest;
use App\Http\Requests\IndividualRegisterRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Http\Controllers\backend\NotificationController;


class UserController extends Controller
{
  /**
   * Handles the registration of a new individual user including saving their address,
   * personal information, and uploading an image. It performs a transactional operation
   * to ensure atomicity of the user creation process.
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

      $userIndividual = user_individual::create([
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

      $userNotification = NotificationController::userRegisterNotif($user);

      user_notification::create([
        'user_id' => $user->id,
        'notif_type' => 1,
        'title' => $userNotification['title'],
        'message' => $userNotification['description'],
        'status' => 'unread',
      ]);

      $AdminNotification = NotificationController::adminRegisterNotif($user); // Call the 'adminRegisterNotif' function using the correct namespace

      user_admin_notification::create([
        'user_id' => $user->id,
        'notif_type' => 1,
        'title' => $AdminNotification['title'],
        'message' => $AdminNotification['description'],
        'status' => 'unread',
      ]);


      $this->sendOtpEmail($user);

      DB::commit();

      return response()->json([
        'success' => true,
        'data' => [
          'email' => $user->email,
        ],
        'message' => 'User registered successfully. Please verify your email.',
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
   * Handles the registration of a new company user including saving the company's address,
   * representative's information, and uploading an image. It also ensures the process is
   * transactional to maintain database integrity.
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

      $userNotification = NotificationController::userRegisterNotif($user);

      user_notification::create([
        'user_id' => $user->id,
        'notif_type' => 1,
        'title' => $userNotification['title'],
        'message' => $userNotification['description'],
        'status' => 'unread',
      ]);

      $AdminNotification = NotificationController::adminRegisterNotif($user); // Call the 'adminRegisterNotif' function using the correct namespace

      user_admin_notification::create([
        'user_id' => $user->id,
        'notif_type' => 1,
        'title' => $AdminNotification['title'],
        'message' => $AdminNotification['description'],
        'status' => 'unread',
      ]);


      $this->sendOtpEmail($user);

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
   * Resends the OTP code to the user's email.
   */
  public function resendOtpCode(Request $request): JsonResponse
  {
    $request->validate([
      'email' => 'required|email',
    ]);

    $user = User::where('email', $request->input('email'))->first();

    if (!$user) {
      return response()->json([
        'success' => false,
        'message' => 'User not found',
      ], 404);
    }

    // Generate a new OTP code
    $otpCode = mt_rand(100000, 999999);

    // Update the user's OTP code in the database
    $user->otpCodes()->updateOrCreate([], ['otp_codes' => $otpCode]);

    // Send the OTP code to the user's email
    $this->sendOtpEmail($user);

    return response()->json([
      'success' => true,
      'message' => 'OTP code has been resent to your email',
    ]);
  }


  /**
   * Authenticates a user by email and password, generates a new token for the session,
   * and returns user message along with the token. It uses custom validation and returns
   * a JSON response.
   */
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

    if ($user->activation !== 'active') {
      return response()->json([
        'success' => false,
        'message' => 'User is not active, please verify your email.',
      ], 401);
    }

    if (!$user || !Hash::check($credentials['password'], $user->password)) {
      return response()->json([
        'success' => false,
        'message' => 'Invalid credentials',
      ], 401);
    }

    $token = $user->createToken($credentials['email'])->plainTextToken;

    return response()->json([
      'success' => true,
      'token' => $token,
      'message' => 'User logged in successfully',
    ]);
  }

  /**
   * Retrieves detailed information about the authenticated user. This includes the
   * user's personal or company information, address, and profile image. It returns this
   * information as a JSON response.
   */
  public function getUserData(): JsonResponse
  {
    // Mengambil ID pengguna dari token
    $userId = auth()->user()->id;

    // Mengambil user berdasarkan ID
    $user = User::find($userId);

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
        return $query->select('address_id')->from('user_individuals')->where('user_id', $user->id);
      } else {
        return $query->select('address_id')->from('user_companies')->where('user_id', $user->id);
      }
    })->first();

    // Memuat data tambahan berdasarkan tipe user
    if ($user->user_type == 'individual') {
      $additionalData = user_individual::where('user_id', $user->id)->first();
    } else {
      $additionalData = User_company::where('user_id', $user->id)->first();
    }

    // Membangun response
    return response()->json([
      'success' => true,
      'data' => [
        'user' => $user,
        'user_additional_data' => $additionalData, // Ini adalah user_individual atau user_company
        'address' => $address,
        'user_image' => $userImage,
      ],
    ]);
  }


  /**
   * Retrieves detailed information about a specific user by their ID. This includes the
   * user's personal or company information, address, and profile image. It returns this
   * information as a JSON response.
   */
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
        return $query->select('address_id')->from('user_individuals')->where('user_id', $user->id);
      } else {
        return $query->select('address_id')->from('user_companies')->where('user_id', $user->id);
      }
    })->first();

    // Memuat data tambahan berdasarkan tipe user
    if ($user->user_type == 'individual') {
      $additionalData = user_individual::where('user_id', $user->id)->first();
    } else {
      $additionalData = User_company::where('user_id', $user->id)->first();
    }

    // Membangun response
    return response()->json([
      'success' => true,
      'data' => [
        'user' => $user,
        'user_additional_data' => $additionalData, // Ini adalah user_individual atau user_company
        'address' => $address,
        'user_image' => $userImage,
      ],
    ]);
  }

  /**
   * Retrieves information about all users including their personal or company information,
   * addresses, and profile images. It returns this information as a JSON response.
   */
  public function getAllUsers(): JsonResponse
  {
    $users = User::all();

    $transformedUsers = [];

    foreach ($users as $user) {
      $userImage = user_image::where('user_id', $user->id)->first();
      $address = Address::where('id', function ($query) use ($user) {
        if ($user->user_type == 'individual') {
          return $query->select('address_id')->from('user_individuals')->where('user_id', $user->id);
        } else {
          return $query->select('address_id')->from('user_companies')->where('user_id', $user->id);
        }
      })->first();

      if ($user->user_type == 'individual') {
        $additionalData = user_individual::where('user_id', $user->id)->first();
      } else {
        $additionalData = User_company::where('user_id', $user->id)->first();
      }

      $transformedUsers[$user->id] = [
        'user' => $user,
        'user_additional_data' => $additionalData, // Ini adalah UserIndividual atau UserCompany
        'address' => $address,
        'user_image' => $userImage,
      ];
    }

    return response()->json([
      'success' => true,
      'data' => $transformedUsers,
    ]);
  }

  /**
   * Logs out the currently authenticated user by revoking their token. It returns a JSON
   * response indicating the logout was successful or that the user was unauthenticated.
   */
  public function logout(Request $request)
  {
    $user = $request->user();

    if ($user) {
      $user->currentAccessToken()->delete();
      return response()->json(['message' => 'You have been successfully logged out.'], 200);
    }

    return response()->json(['message' => 'Unauthenticated.'], 401);
  }

  /**
   * Fungsi untuk mengirimkan OTP via email
   *
   * @param User $user
   */
  protected function sendOtpEmail($user)
  {
    // Generate OTP
    $otp = rand(100000, 999999);
    $otpExpiry = now()->addMinutes(10); // Set OTP to expire in 10 minutes

    // Save OTP to database
    otp_code::create([
      'user_id' => $user->id,
      'user_email' => $user->email,
      'otp_codes' => $otp,
      'expired_at' => $otpExpiry,
    ]);

    // Send OTP via email
    Mail::to($user->email)->send(new SendOtpMail($otp));
  }

  /**
   * Verifikasi OTP.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function verifyOtp(Request $request)
  {
    $request->validate([
      'email' => 'required|email',
      'otp_code' => 'required|numeric',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user) {
      return response()->json([
        'success' => false,
        'message' => 'User not found.'
      ], 404);
    }

    $otpRecord = otp_code::where('user_email', $request->email)
      ->where('otp_codes', $request->otp_code) // Pastikan nama kolom sesuai dengan database Anda
      ->first();

    if (!$otpRecord) {
      return response()->json([
        'success' => false,
        'message' => 'OTP is invalid or does not exist.'
      ], 400);
    }

    if ($otpRecord->status == 'verified') {
      return response()->json([
        'success' => false,
        'message' => 'This OTP has already been used.'
      ], 400);
    }

    if (Carbon::now()->gt(Carbon::parse($otpRecord->expired_at))) {
      return response()->json([
        'success' => false,
        'message' => 'This OTP has expired.'
      ], 400);
    }

    try {
      // Mark OTP as verified
      $otpRecord->status = 'verified';
      $otpRecord->save();

      // Update user activation status
      $user->activation = 'active';
      $user->save();

      return response()->json([
        'success' => true,
        'message' => 'OTP verification successful and user activated.'
      ], 200);
    } catch (\Exception $e) {
      return response()->json([
        'error' => 'An unexpected error occurred during the verification process.',
        'message' => $e->getMessage()
      ], 500);
    }
  }

  /**
   * Fungsi untuk login admin.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function adminlogin(Request $request)
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

    if ($user->activation !== 'active') {
      return response()->json([
        'success' => false,
        'message' => 'User is not active, please verify your email.',
      ], 401);
    }

    if ($user->role_id != 3) {
      return response()->json([
        'success' => false,
        'message' => 'Invalid role',
      ], 401);
    }


    if (!$user || !Hash::check($credentials['password'], $user->password)) {
      return response()->json([
        'success' => false,
        'message' => 'Invalid credentials',
      ], 401);
    }

    $token = $user->createToken($credentials['email'])->plainTextToken;

    return response()->json([
      'success' => true,
      'token' => $token,
      'message' => 'User logged in successfully',
    ]);
  }

  /**
   * Resets the password for a user by sending an OTP code to their email.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function resetPassword(Request $request)
  {
    $request->validate([
      'email' => 'required|email',
    ]);
    $user = User::where('email', $request->input('email'))->first();
    if (!$user) {
      return response()->json([
        'success' => false,
        'message' => 'User not found.',
      ], 404);
    }
    // Generate a new OTP code
    $otpCode = mt_rand(100000, 999999);
    // Update the user's OTP code in the database
    $user->otpCodes()->updateOrCreate([], ['otp_codes' => $otpCode]);
    // Send the OTP code to the user's email
    $this->sendOtpEmail($user);
    return response()->json([
      'success' => true,
      'message' => 'OTP code has been sent to your email.',
    ],  200);
  }

  /**
   * Confirms the password reset by verifying the OTP code and updating the user's password.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function confirmResetPassword(Request $request)
  {
    // Validasi input
    $request->validate([
      'email' => 'required|email',
      'otp_code' => 'required|numeric',
      'new_password' => 'required|min:8',
      'confirm_password' => 'required|same:new_password',
    ]);

    // Cari user berdasarkan email
    $user = User::where('email', $request->input('email'))->first();
    if (!$user) {
      return response()->json([
        'success' => false,
        'message' => 'User not found.',
      ], 404);
    }

    // Cari OTP terbaru berdasarkan email
    $otpRecord = otp_code::where('user_email', $request->input('email'))
      ->orderBy('created_at', 'desc')
      ->first();
    if (!$otpRecord) {
      return response()->json([
        'success' => false,
        'message' => 'OTP is invalid or does not exist.',
      ], 400);
    }

    // Cek status OTP
    if ($otpRecord->status == 'verified') {
      return response()->json([
        'success' => false,
        'message' => 'This OTP has already been used.',
      ], 400);
    }

    // Cek apakah OTP telah expired
    if (Carbon::now()->gt(Carbon::parse($otpRecord->expired_at))) {
      return response()->json([
        'success' => false,
        'message' => 'This OTP has expired.',
      ], 400);
    }

    try {
      // Verifikasi kode OTP
      if ($otpRecord->otp_codes != $request->input('otp_code')) {
        return response()->json([
          'success' => false,
          'message' => 'Invalid OTP code.',
        ], 400);
      }

      // Update password user
      $user->password = Hash::make($request->input('new_password'));
      $user->save();

      // Tandai OTP sebagai 'verified'
      $otpRecord->status = 'verified';
      $otpRecord->save();

      return response()->json([
        'success' => true,
        'message' => 'Password reset successful.',
      ], 200);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => $e->getMessage(),
      ], 500);
    }
  }

  /**
   * Edits a user's information based on their ID.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function editUser(Request $request)
  {
    try {
      $userId = auth()->user()->id;
      // Mengambil user berdasarkan ID
      $user = User::find($userId);
      // Cari user berdasarkan ID
      $user = User::find();
      if (!$user) {
        return response()->json([
          'success' => false,
          'message' => 'User not found.',
        ], 404);
      }
      if ($user->usertype == 'individual') {
        $request->validate([
          'email' => 'sometimes|email|unique:users,email,' . $user->id,
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
        ]);
      } else {
        $request->validate([
          'email' => 'sometimes|email|unique:users,email,' . $user->id,
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
        ]);
      }
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => $e->getMessage(),
      ], 500);
    }
  }
}
