<?php

use App\Http\Controllers\LandContentController;
use App\Http\Controllers\LandController;
use App\Http\Controllers\MappingTypeController;
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

// Group routes with 'auth:sanctum' middleware to avoid repetition
Route::middleware('auth:sanctum')->group(function () {
    // User related routes
    Route::get('/users/{id}', [UserController::class, 'getUser']);
    Route::get('/logout', [UserController::class, 'logout']);

    // Land CRUD operations
    Route::post('/lands', [LandController::class, 'store']);

    // Land Content CRUD operations
    Route::get('/land-content-histories', [LandContentController::class, 'index']);
    Route::get('/land-content-histories/{id}', [LandContentController::class, 'show']);
    Route::post('/land-content-histories', [LandContentController::class, 'store']);
    Route::delete('/land-content-histories/{id}', [LandContentController::class, 'destroy']);


    // Admin specific routes with additional AdminAuthorization middleware
    Route::middleware('AdminAuthorization')->group(function () {

        // User for admin
        Route::get('/allusers', [UserController::class, 'getAllUsers']);

        // MappingType CRUD operations
        Route::get('/mapping-types', [MappingTypeController::class, 'index']);
        Route::post('/mapping-types', [MappingTypeController::class, 'store']);
        Route::get('/mapping-types/{mappingType}', [MappingTypeController::class, 'show']);
        Route::put('/mapping-types/{mappingType}', [MappingTypeController::class, 'update']);
        Route::delete('/mapping-types/{mappingType}', [MappingTypeController::class, 'destroy']);
    });
});

// Registration and Login routes that don't require authentication
Route::post('/individuals', [UserController::class, 'registerIndividual']);
Route::post('/companies', [UserController::class, 'registerCompany']);
Route::post('/login', [UserController::class, 'login'])->name('login');
