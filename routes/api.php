<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\AppUser\ReviewController;
use App\Http\Controllers\AppUser\ApartmentController;
use App\Http\Controllers\AppUser\BookedApartmentController;
<<<<<<< HEAD
use App\Http\Controllers\AppUser\FavoriteController;
=======
use App\Http\Controllers\AppUser\UserProfileController;
>>>>>>> ef788dc2e94ec66b9fce3c392c93fbe7f98c2017

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

//reviews route

Route::post('/review', [ReviewController::class, 'store']);
Route::post('/review/{review}', [ReviewController::class, 'update']);
Route::delete('/review/{review}', [ReviewController::class, 'destroy']);

//////booked
Route::get('/booked', [BookedApartmentController::class, 'index']);
Route::get('/booked/{booked}', [BookedApartmentController::class, 'show']);
Route::post('/booked', [BookedApartmentController::class, 'store']);
Route::post('/booked/{Booked_apartment}', [BookedApartmentController::class, 'update']);
Route::delete('/booked/{Booked_apartment}', [BookedApartmentController::class, 'destroy']);

<<<<<<< HEAD
//favorit route

Route::post('/favorit', [FavoriteController::class, 'store']);
Route::post('/favorit/{favorit}', [FavoriteController::class, 'update']);
Route::delete('/favorit/{favorit}', [FavoriteController::class, 'destroy']);

=======
//apartments
Route::post('/search/apartments', [ApartmentController::class, 'search']);
///user profile
Route::get('/user-review', [UserProfileController::class, 'reviews']);
Route::get('/user-favorit', [UserProfileController::class, 'favorit']);
Route::get('/user-booked', [UserProfileController::class, 'booked']);
Route::get('/user-settings', [UserProfileController::class, 'settings']);
>>>>>>> ef788dc2e94ec66b9fce3c392c93fbe7f98c2017
require __DIR__ . '/dashboard.php';
