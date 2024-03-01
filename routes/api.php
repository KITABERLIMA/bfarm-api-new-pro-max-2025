<?php

use App\Http\Controllers\LandController;
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
    Route::get('/users/{id}', [UserController::class, 'getUser']);
    Route::get('/logout', [UserController::class, 'logout']);
    Route::post('/lands', [LandController::class, 'store']);

    // Use array to apply multiple middleware
    Route::get('/allusers', [UserController::class, 'getAllUsers'])->middleware('AdminAuthorization');
});

// Registration and Login routes that don't require authentication
Route::post('/individuals', [UserController::class, 'registerIndividual']);
Route::post('/companies', [UserController::class, 'registerCompany']);
Route::post('/login', [UserController::class, 'login'])->name('login');
