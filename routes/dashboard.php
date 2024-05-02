<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AreaController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PriceController;
use App\Http\Controllers\Admin\TermsController;
use App\Http\Controllers\Admin\OffersController;
use App\Http\Controllers\Admin\AboutUsController;
use App\Http\Controllers\Admin\PrivacyController;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\ContacUsController;
use App\Http\Controllers\Admin\ApartmentController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\PaymentGatewayController;
use App\Http\Controllers\Admin\ManualNotificationController;
use App\Http\Controllers\Admin\ControlNotificationController;
use App\Http\Controllers\Admin\CouponsController;
use App\Http\Controllers\Admin\DiscountController;
use App\Http\Controllers\Admin\InformationController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\PointController;


Route::group([
    'prefix' => 'auth'
], function ($router) {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
});
Route::group([
    'middleware' => 'auth:users',
    'prefix' => 'dashboard',
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
Route::get('/areas/{city_id}', [AreaController::class, 'cityArea']);
//apartment route

Route::get('/apartment', [ApartmentController::class, 'index']);
Route::get('/apartment/{apartment}', [ApartmentController::class, 'show']);
Route::post('/apartment', [ApartmentController::class, 'store']);
Route::post('/apartment/{apartment}', [ApartmentController::class, 'update']);
Route::delete('/apartment/{apartment}', [ApartmentController::class, 'destroy']);
Route::post('/change-status', [ApartmentController::class, 'changeStatus']);
Route::get('apartmentCount', [ApartmentController::class, 'apartmentCount']);
Route::post('/prices', [PriceController::class, 'store']);
Route::get('/prices/{id}', [PriceController::class, 'show']);
Route::post('/apartments/{id}/copy', [ApartmentController::class, 'copyApartment']);
/////////about_us
Route::get('/about_us', [AboutUsController::class, 'index']);
Route::get('/about_us/{about_us}', [AboutUsController::class, 'show']);
Route::post('/about_us', [AboutUsController::class, 'store']);
Route::post('/about_us/{about_us}', [AboutUsController::class, 'update']);
Route::delete('/about_us/{about_us}', [AboutUsController::class, 'destroy']);

/////////privacy
Route::get('/privacy', [PrivacyController::class, 'index']);
Route::get('/privacy/{privacy}', [PrivacyController::class, 'show']);
Route::post('/privacy', [PrivacyController::class, 'store']);
Route::post('/privacy/{privacy}', [PrivacyController::class, 'update']);
Route::delete('/privacy/{privacy}', [PrivacyController::class, 'destroy']);

/////////terms
Route::get('/terms', [TermsController::class, 'index']);
Route::get('/terms/{term}', [TermsController::class, 'show']);
Route::post('/terms', [TermsController::class, 'store']);
Route::post('/terms/{term}', [TermsController::class, 'update']);
Route::delete('/terms/{term}', [TermsController::class, 'destroy']);


//setting
Route::get('/setting', [SettingController::class, 'index']);
Route::post('/setting', [SettingController::class, 'store']);

//payments getway
Route::get('/payments-getway', [PaymentGatewayController::class, 'index']);
Route::post('/myFatoorah-update', [PaymentGatewayController::class, 'MyfatoorahUpdate']);
//reports
Route::get('/all-order', [ReportsController::class, 'all_orders']);
Route::get('orderCount', [OrderController::class, 'orderCount']);
Route::get('/all-payments', [ReportsController::class, 'all_payments']);
Route::get('/reservation-request', [ReportsController::class, 'reservation_request']);
//offers

Route::get('/offers', [OffersController::class, 'index']);
Route::get('/offers/{offer}', [OffersController::class, 'show']);
Route::post('/offers', [OffersController::class, 'store']);
Route::post('/offers/{offer}', [OffersController::class, 'update']);
Route::delete('/offers/{offer}', [OffersController::class, 'destroy']);


///notifications
Route::get('/notification-read', [NotificationController::class, 'NotificationRead']);
Route::get('/notification-markasread', [NotificationController::class, 'MarkASRead']);
Route::get('/notification-clear', [NotificationController::class, 'Clear']);

///manual notifications
Route::post('/manual-notifications', [ManualNotificationController::class, 'store']);
//control notification

Route::get('/control-notifications', [ControlNotificationController::class, 'index']);
Route::post('/control-notifications', [ControlNotificationController::class, 'store']);
Route::post('/control-notification/{notification}', [ControlNotificationController::class, 'update']);
Route::delete('/control-notification/{notification}', [ControlNotificationController::class, 'destroy']);
///contact us
Route::get('/contact-us', [ContacUsController::class, 'index']);
//
Route::get('/app-users', [UserController::class, 'All']);
//Information
Route::get('information', [InformationController::class, 'index']);
Route::post('information', [InformationController::class, 'store']);
Route::get('information/{Information}', [InformationController::class, 'show']);
Route::post('information/{Information}', [InformationController::class, 'update']);
Route::delete('information/{Information}', [InformationController::class, 'destroy']);
//reviews
Route::get('/reviews', [ReviewController::class, 'index']);
Route::get('/reviews/{id}', [ReviewController::class, 'show']);
Route::delete('/reviews/{id}', [ReviewController::class, 'destroy']);
//Discount
Route::get('/discounts', [DiscountController::class, 'index']);
Route::post('/discounts', [DiscountController::class, 'store']);
Route::get('/discounts/{discount}', [DiscountController::class, 'show']);
Route::post('/discounts/{discount}', [DiscountController::class, 'update']);
Route::delete('/discounts/{discount}', [DiscountController::class, 'destroy']);

///

Route::get('/coupons', [CouponsController::class, 'index']);
Route::post('/coupons', [CouponsController::class, 'store']);
Route::get('/coupons/{coupon}', [CouponsController::class, 'show']);
Route::post('/coupons/{coupon}', [CouponsController::class, 'update']);
Route::delete('/coupons/{coupon}', [CouponsController::class, 'destroy']);

//point
Route::get('/balance', [PointController::class, 'index']);

});

