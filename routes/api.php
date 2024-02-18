<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\AppUser\AuthController;
use App\Http\Controllers\AppUser\HomeController;
use App\Http\Controllers\AppUser\ReviewController;

use App\Http\Controllers\AppUser\AppUsersController;

use App\Http\Controllers\AppUser\FavoriteController;
use App\Http\Controllers\AppUser\ApartmentController;
use App\Http\Controllers\AppUser\UserProfileController;
use App\Http\Controllers\AppUser\BookedApartmentController;


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
Route::group([
    'prefix' => 'app_user/auth'
], function ($router) {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/check_number', [AppUsersController::class, 'check_number']);
    Route::post('/check_opt', [AppUsersController::class, 'check_opt']);
    Route::post('/register', [AuthController::class, 'register']);
});

Route::group([
    'middleware' => 'auth:app_users',

], function ($router) {
//reviews route
Route::post('/review', [ReviewController::class, 'store']);
Route::post('/review/{review}', [ReviewController::class, 'update']);
Route::delete('/review/{review}', [ReviewController::class, 'destroy']);
//favorit route
Route::post('/favorit', [FavoriteController::class, 'store']);
Route::post('/update-favorit', [FavoriteController::class, 'update']);
//////booked
Route::get('/booked', [BookedApartmentController::class, 'index']);
Route::get('/booked/{booked}', [BookedApartmentController::class, 'show']);
Route::post('/booked', [BookedApartmentController::class, 'store']);
Route::post('/booked/{Booked_apartment}', [BookedApartmentController::class, 'update']);
Route::get('/canceld-booked/{Booked_apartment}', [BookedApartmentController::class, 'canceld']);
Route::delete('/booked/{Booked_apartment}', [BookedApartmentController::class, 'destroy']);
///invest-user
Route::post('/invest-user', [ApartmentController::class, 'store']);
//reports booked
Route::get('user-booked', [BookedApartmentController::class, 'userBooked']);

//////payments
Route::post('/callback', [BookedApartmentController::class, 'callback'])->name('callback');
Route::post('/error', [BookedApartmentController::class, 'error'])->name('error');
///user profile
Route::get('/user-review', [UserProfileController::class, 'reviews']);
Route::get('/user-favorit', [UserProfileController::class, 'favorit']);
Route::get('/user-settings', [UserProfileController::class, 'settings']);
});
//apartments
Route::post('/search/apartments', [ApartmentController::class, 'search']);

//
Route::get('/about_us', [HomeController::class, 'about_us']);
Route::get('/privacy', [HomeController::class, 'privacy']);
Route::get('/terms', [HomeController::class, 'terms']);
//areas
Route::get('/cities', [HomeController::class, 'cities']);
Route::get('/areas', [HomeController::class, 'areas']);
require __DIR__ . '/dashboard.php';
