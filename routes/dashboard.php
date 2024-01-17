<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AuthController;

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
// Route::get('/roles', [RoleController::class, 'index']);
// Route::get('/roles/{role}', [RoleController::class, 'show']);
// Route::post('/roles', [RoleController::class, 'store']);
// Route::post('/roles/{role}', [RoleController::class, 'update']);
// Route::delete('/roles/{role}', [RoleController::class, 'destroy']);
});





