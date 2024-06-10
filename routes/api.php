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
use App\Http\Controllers\AppUser\NotificationController;
use App\Http\Controllers\AppUser\BookedApartmentController;
use App\Http\Controllers\AppUser\PointController;

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
//Route::get('/user-reviews', [ReviewController::class, 'index']);
Route::get('/review/{id}', [ReviewController::class, 'show']);
//favorit route
Route::post('/favorit', [FavoriteController::class, 'store']);
Route::post('/update-favorit', [FavoriteController::class, 'update']);
Route::get('/user-favorits', [FavoriteController::class, 'index']);
//////booked
Route::get('/booked', [BookedApartmentController::class, 'index']);
Route::get('/booked/{booked}', [BookedApartmentController::class, 'show']);
Route::post('/booked', [BookedApartmentController::class, 'store']);
Route::post('/canceld-booked', [BookedApartmentController::class, 'canceld']);
Route::delete('/booked/{Booked_apartment}', [BookedApartmentController::class, 'destroy']);
Route::get('user-booked', [BookedApartmentController::class, 'userBooked']);
Route::get('user-booked-details/{id}', [BookedApartmentController::class, 'userBookedDetailsAccess']);
Route::post('user-leaving', [BookedApartmentController::class, 'userLeaving']);
Route::get('/generate-pdf', [BookedApartmentController::class, 'generate_pdf']);

///coupon
Route::post('check-coupon', [BookedApartmentController::class, 'checkCoupon']);
///invest-user
Route::post('/invest-user', [ApartmentController::class, 'store']);

//////payments
///user profile
Route::get('/user-profile', [UserProfileController::class, 'index']);
Route::post('/update-profile', [UserProfileController::class, 'updateProfile']);
Route::get('/deactive-account', [UserProfileController::class, 'deactive_account']);
Route::get('/my-apartments', [UserProfileController::class, 'myApartments']);//owner
Route::get('/sold-apartments', [UserProfileController::class, 'SolidApartments']);//owner
Route::get('/balance', [PointController::class, 'index']);

///notifications
Route::get('/notification-read', [NotificationController::class, 'NotificationRead']);
Route::get('/notification-markasread', [NotificationController::class, 'MarkASRead']);
Route::get('/notification-clear', [NotificationController::class, 'Clear']);
//apartments
Route::post('/search/apartments', [ApartmentController::class, 'search']);
});

//apartments
Route::get('/all-apartments', [ApartmentController::class, 'allApartments']);
///////////
Route::get('/about_us', [HomeController::class, 'about_us']);
Route::get('/privacy', [HomeController::class, 'privacy']);
Route::get('/terms', [HomeController::class, 'terms']);
Route::get('/setting', [HomeController::class, 'settings']);
Route::get('/payment-getway', [HomeController::class, 'paymentGetway']);
//areas
Route::get('/cities', [HomeController::class, 'cities']);
Route::get('/areas', [HomeController::class, 'areas']);
//offers
Route::get('/offers', [HomeController::class, 'offers']);
Route::get('callback', [BookedApartmentController::class, 'callback'])->name('callback');
Route::get('error', [BookedApartmentController::class, 'error'])->name('error');

Route::get('/tabby-sucess', [BookedApartmentController::class, 'sucess'])->name('success-ur');
Route::get('/tabby-cancel', [BookedApartmentController::class, 'cancel'])->name('cancel-ur');
Route::get('/tabby-failure', [BookedApartmentController::class, 'failure'])->name('failure-ur');



/////home page web

Route::post('/contact-us', [App\Http\Controllers\HomeController::class, 'contactUs']);
Route::get('/home-settings', [App\Http\Controllers\HomeController::class, 'Settings']);
require __DIR__ . '/dashboard.php';
