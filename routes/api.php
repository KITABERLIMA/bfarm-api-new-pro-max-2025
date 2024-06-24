<?php

use App\Http\Controllers\backend\{
  InventoryController,
  LandContentController,
  LandController,
  MappedLandController,
  MappingTypeController,
  NotificationController,
  ProductController,
  SubscriptionController,
  SubsTransactionController,
  SuperAdminController,
  UserController
};
use App\Http\Controllers\UserLandController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Group routes with 'otpVerification' middleware
Route::middleware('otpVerification')->group(function () {
  // Group routes with 'auth:sanctum' middleware
  Route::middleware('auth:sanctum')->group(function () {
    // User related routes
    Route::prefix('users')->group(function () {
      Route::get('/{id}', [UserController::class, 'getUser']);
      Route::post('/{id}', [UserController::class, 'editUser']);
      Route::get('/', [UserController::class, 'getUserData']);
      Route::post('/changepassword', [UserController::class, 'ChangePasswordUser']);
      Route::get('/logout', [UserController::class, 'logout']);
    });

    // Land CRUD simplified using apiResource
    Route::apiResource('lands', LandController::class);
    Route::prefix('land')->group(function () {
      Route::get('/list', [LandController::class, 'listAll']);
      Route::get('/mapped', [LandController::class, 'listMapped']);
      Route::get('/unmapped', [LandController::class, 'listUnmapped']);
    });

    // New subscription transaction
    Route::post('/subs-transaction', [SubsTransactionController::class, 'store']);

    // Land Content CRUD operations simplified
    Route::apiResource('land-contents', LandContentController::class);

    Route::get('user-land', [UserLandController::class, 'index']);

    Route::middleware('authorization')->group(function () {

      Route::post('user-land/edit/{land}', [UserLandController::class, 'update']);
      Route::delete('user-land/delete/{land}', [UserLandController::class, 'delete']);
    });

    // Admin specific routes with additional AdminAuthorization middleware
    Route::middleware('AdminAuthorization')->group(function () {
      // Mapped Lands CRUD operations simplified

      // Simplify Inventory CRUD operations
      Route::apiResource('inventories', InventoryController::class);

      // Product CRUD Routes simplified
      Route::apiResource('products', ProductController::class);

      // Users for admin
      Route::get('/allusers', [UserController::class, 'getAllUsers']);

      // Mapping Type CRUD operations simplified
      Route::apiResource('mapping-types', MappingTypeController::class);

      // Subscription CRUD operations simplified
      Route::apiResource('subscriptions', SubscriptionController::class);

      // Subs transaction CRUD operations simplified
      Route::apiResource('subs-transactions', SubsTransactionController::class);

      // Custom Promo Notification routes
      Route::post('/custom-promo-notif', [NotificationController::class, 'customPromotionNotif']);
      Route::post('/custom-promo-notif-all', [NotificationController::class, 'customPromoNotifForAll']);
      Route::post('/notif', [NotificationController::class, 'store']);

      // Super Admin specific routes
      Route::middleware('SuperAdminAuthorization')->group(function () {
        Route::post('/role/{users}', [SuperAdminController::class, 'rolechanger']);
      });
    });
  });
});

// Routes that don't require authentication
Route::post('/login', [UserController::class, 'login']);
Route::post('/resetPassword', [UserController::class, 'resetPassword']);
Route::post('/confirmResetPassword', [UserController::class, 'confirmResetPassword']);
Route::post('/individuals', [UserController::class, 'registerIndividual']);
Route::post('/companies', [UserController::class, 'registerCompany']);
Route::post('/otpverification', [UserController::class, 'verifyOtp']);
Route::post('/resendOtpCode', [UserController::class, 'resendOtpCode']);

// Admin login
Route::post('/admin/login', [UserController::class, 'adminlogin']);