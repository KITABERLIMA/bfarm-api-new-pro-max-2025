<?php

use App\Http\Controllers\InventoryController;
use App\Http\Controllers\LandContentController;
use App\Http\Controllers\LandController;
use App\Http\Controllers\MappedLandController;
use App\Http\Controllers\MappingTypeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\SubsTransactionController;
use App\Http\Controllers\superAdminController;
use App\Http\Controllers\UserController;
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

// Group routes with 'auth:sanctum' middleware to ensure only authenticated users can access
Route::middleware('auth:sanctum')->group(function () {
    // User related routes
    Route::get('/users/{id}', [UserController::class, 'getUser']);
    Route::get('/logout', [UserController::class, 'logout']);

    // Land CRUD simplified using apiResource
    Route::apiResource('lands', LandController::class); // Assuming index and show are not needed
    // List lands
    Route::get('/land/list', [LandController::class, 'listAll']);
    Route::get('/land/mapped', [LandController::class, 'listMapped']);
    Route::get('/land/unmapped', [LandController::class, 'listunmapped']);

    // new subs transaction
    Route::post('/subs-transaction', [SubsTransactionController::class, 'store']);

    // Land Content CRUD operations simplified
    Route::apiResource('land-contents', LandContentController::class); // Assuming update is not needed

    // Mapped Lands CRUD operations simplified
    Route::apiResource('mapped-lands', MappedLandController::class);

    // Admin specific routes with additional AdminAuthorization middleware
    Route::middleware('AdminAuthorization')->group(function () {
        // Simplify Inventory CRUD operations
        Route::apiResource('inventories', InventoryController::class);

        // Product CRUD Routes simplified
        Route::apiResource('products', ProductController::class);

        // Users for admin
        Route::get('/allusers', [UserController::class, 'getAllUsers']);

        // Mapping Type CRUD operations simplified
        Route::apiResource('mapping-types', MappingTypeController::class);

        // subscription CRUD operations simplified
        Route::apiResource('subscriptions', SubscriptionController::class);

        // subs_transaction CRUD operations simplified
        Route::apiResource('subs-transactions', SubsTransactionController::class);

        // Custom Promo Notification routes
        Route::post('/custom-promo-notif', [NotificationController::class, 'customPromotionNotif']);
        Route::post('/custom-promo-notif-all', [NotificationController::class, 'customPromoNotifForAll']);

        Route::middleware('SuperAdminAuthorization')->group(function () {
            Route::post('/role/{users}', [superAdminController::class, 'rolechanger']);
        });
    });
});

// Registration and Login routes that don't require authentication
Route::post('/individuals', [UserController::class, 'registerIndividual']);
Route::post('/companies', [UserController::class, 'registerCompany']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/otpverification', [UserController::class, 'verifyOtp']);
