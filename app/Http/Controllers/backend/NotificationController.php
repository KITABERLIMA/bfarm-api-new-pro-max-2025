<?php

namespace App\Http\Controllers\backend;

use App\Models\User_company;
use App\Models\user_individual;
use App\Models\user_admin_notification;
use App\Http\Requests\NotificationRequest;
use App\Http\Requests\Updateuser_admin_notificationRequest;
use App\Models\user;
use App\Models\user_notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;


class NotificationController extends Controller
{

  /**
   * Get the user's name from user_company or user_individual table based on user_id.
   * Create a sentence with the name and return it.
   */
  public static function getNameById($user_id)
  {
    $user_company = User_company::where('user_id', $user_id)->first();
    $user_individual = user_individual::where('user_id', $user_id)->first();

    if ($user_company) {
      $name = $user_company->name;
    } elseif ($user_individual) {
      $name = $user_individual->name;
    } else {
      $name = 'Unknown';
    }

    return $name;
  }

  public static function userRegisterNotif($user)
  {
    $name = self::getNameById($user->id);

    $title = "Welcome, $name!";
    $description = "Thank you for registering. We are excited to have you as part of our community. Explore our platform and discover the amazing features we offer.";

    $notification = [
      'title' => $title,
      'description' => $description
    ];

    return $notification;
  }

  public static function adminRegisterNotif($user)
  {
    $name = self::getNameById($user->id);

    $title = "New User, $name";
    $description = "A new user, $name, has registered on our platform.";

    $notification = [
      'title' => $title,
      'description' => $description
    ];

    return $notification;
  }

  public static function userPurchaseNotif($user, $product)
  {
    $name = self::getNameById($user->id);

    $title = "Congratulations, $name!";
    $description = "Thank you for purchasing $product->name. We hope you enjoy using it.";

    $notification = [
      'title' => $title,
      'description' => $description
    ];

    return $notification;
  }

  public static function adminPurchaseNotif($user, $product)
  {
    $name = self::getNameById($user->id);

    $title = "New Purchase, $name";
    $description = "A user, $name, has purchased $product->name.";

    $notification = [
      'title' => $title,
      'description' => $description
    ];

    return $notification;
  }

  public static function customPromotionNotif(Request $request)
  {
    $productId = $request->input('product_id');
    $userId = $request->input('user_id');

    // Validate input
    $validator = Validator::make($request->all(), [
      'product_id' => 'required|integer|exists:products,id',
      'user_id' => 'required|integer|exists:users,id',
    ]);

    if ($validator->fails()) {
      return response()->json(['error' => $validator->errors()], 400);
    }

    $name = self::getNameById($userId);

    $title = "Special Promotion for $name!";
    $description = "Don't miss out on our special promotion for $productId. Get it now at a discounted price.";

    $notification = [
      'title' => $title,
      'description' => $description
    ];

    return $notification;
  }

  public static function customPromoNotifForAll(Request $request)
  {
    $productId = $request->input('product_id');

    // Validate product_id
    $validator = Validator::make($request->all(), [
      'product_id' => 'required|integer|exists:products,id',
    ]);

    if ($validator->fails()) {
      return response()->json(['error' => $validator->errors()], 400);
    }

    $users = User::all();
    $notifications = [];

    foreach ($users as $user) {
      $name = self::getNameById($user->id);

      $title = "Special Promotion for $name!";
      $description = "Don't miss out on our special promotion for $productId. Get it now at a discounted price.";

      $notification = [
        'title' => $title,
        'description' => $description
      ];

      $notifications[] = $notification;
    }

    return $notifications;
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(NotificationRequest $request)
  {
    // Retrieve the validated data from the request
    $data = $request->validated();

    // Get all users with role = 1
    $users = user::where('role', 1)->get();

    // Create notifications for each user
    foreach ($users as $user) {
      // Create a new user_notification instance
      $notification = new user_notification();

      // Set the attributes of the notification
      $notification->user_id = $user->id;
      $notification->notif_type = $data['notif_type'];
      $notification->title = $data['title'];
      $notification->message = $data['message'];

      // Save the notification to the database
      $notification->save();
    }

    // Return a success response
    return response()->json([
      'status' => 'success',
      'message' => 'Notifications created successfully'
    ], 201);
  }

  /**
   * Store a single notification for a specific user.
   */
  public function storeSingle(NotificationRequest $request, $user_id)
  {
    // Retrieve the validated data from the request
    $data = $request->validated();

    // Create a new user_notification instance
    $notification = new user_notification();

    // Set the attributes of the notification
    $notification->user_id = $user_id;
    $notification->notif_type = $data['notif_type'];
    $notification->title = $data['title'];
    $notification->message = $data['message'];

    // Save the notification to the database
    $notification->save();

    // Return a success response
    return response()->json([
      'status' => 'success',
      'message' => 'Notification created successfully'
    ], 201);
  }

  /**
   * Display the specified resource.
   */
  public function show(user_admin_notification $user_admin_notification)
  {
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(user_admin_notification $user_admin_notification)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Updateuser_admin_notificationRequest $request, user_admin_notification $user_admin_notification)
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(user_admin_notification $user_admin_notification)
  {
    //
  }
}
