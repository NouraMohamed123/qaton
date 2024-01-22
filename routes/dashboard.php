<?php

use App\Http\Controllers\Admin\ApartmentController;
use App\Http\Controllers\Admin\AreaController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\RoleController;
use Illuminate\Support\Facades\Route;


Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);


});
Route::group([
    'middleware' => 'api',
    'prefix' => 'dashboard'
], function ($router) {
    //users
Route::get('/users', [UserController::class, 'index']);
Route::get('/users/{user}', [UserController::class, 'show']);
Route::post('/users', [UserController::class, 'store']);
Route::post('/users/{user}', [UserController::class, 'update']);
Route::delete('/users/{user}', [UserController::class, 'destroy']);
Route::get('getUserCount', [UserController::class, 'getUserCount']); 
//roles
Route::get('/roles', [RoleController::class, 'index']);
Route::get('/roles/{role}', [RoleController::class, 'show']);
Route::post('/roles', [RoleController::class, 'store']);
Route::post('/roles/{role}', [RoleController::class, 'update']);
Route::delete('/roles/{role}', [RoleController::class, 'destroy']);
});

//city route

Route::get('/city', [CityController::class, 'index']);
Route::get('/city/{city}', [CityController::class, 'show']);
Route::post('/city', [CityController::class, 'store']);
Route::post('/city/{city}', [CityController::class, 'update']);
Route::delete('/city/{city}', [CityController::class, 'destroy']);


//area route

Route::get('/area', [AreaController::class, 'index']);
Route::get('/area/{area}', [AreaController::class, 'show']);
Route::post('/area', [AreaController::class, 'store']);
Route::post('/area/{area}', [AreaController::class, 'update']);
Route::delete('/area/{area}', [AreaController::class, 'destroy']);

//apartment route

Route::get('/apartment', [ApartmentController::class, 'index']);
Route::get('/apartment/{apartment}', [ApartmentController::class, 'show']);
Route::post('/apartment', [ApartmentController::class, 'store']);
Route::post('/apartment/{apartment}', [ApartmentController::class, 'update']);
Route::delete('/apartment/{apartment}', [ApartmentController::class, 'destroy']);
//reviews route

Route::post('/review', [ReviewController::class, 'store']);
Route::post('/review/{review}', [ReviewController::class, 'update']);
Route::delete('/review/{review}', [ReviewController::class, 'destroy']);


