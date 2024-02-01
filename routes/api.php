<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\AppUser\ReviewController;
use App\Http\Controllers\AppUser\ApartmentController;
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

//apartments
Route::get('/search/apartments', [ApartmentController::class, 'search']);

require __DIR__ . '/dashboard.php';
