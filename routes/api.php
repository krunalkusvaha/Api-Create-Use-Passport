<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AdminAuthController;
use App\Http\Controllers\Api\MultipleUploadController;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('login', [UserController::class, 'login']);
Route::post('register', [UserController::class, 'register']);




Route::group(['middleware' => 'auth:api'], function(){
    Route::get('user-details', [UserController::class, 'get_userDetails']);
    Route::post('user-profile-update', [UserController::class,'user_update_post']);
    Route::post('change-password', [UserController::class,'change_password_post']);
    Route::get('logout', [UserController::class,'logout']);
    
    Route::post('document-upload', [MultipleUploadController::class, 'upload']);

});

Route::group(['prefix' => 'admin', 'namespace' => 'Admin'], function () {
    Route::post('login', [AdminAuthController::class, 'adminLogin']);
    Route::group(['middleware' => 'adminauth'], function () {
        
        Route::get('logout', [AdminAuthController::class,'adminlogout']);
        Route::get('profile', [AdminAuthController::class,'get_admin_profile']);
        Route::post('profile-update', [AdminAuthController::class,'profile_update_post']);
        Route::post('change-password', [AdminAuthController::class,'change_password_post']);

    });
});

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
