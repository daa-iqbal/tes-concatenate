<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
Route::group(['prefix' => 'v1', 'namespace' => 'Api\V1', 'middleware' => ['save.deviceinfo']], function() {
        Route::group(['prefix' => 'autentikasi', 'namespace' => 'Autentikasi'], function() {
            Route::post('signin', 'AutentikasiController@signin');
            Route::post('signin-firebase', 'AutentikasiController@signinFirebase');
            Route::post('signin-bypass', 'AutentikasiController@signinBypass');
            Route::post('signout', 'AutentikasiController@signout')->middleware('auth:api');
            Route::post('signup', 'AutentikasiController@signup');
            Route::post('signup-confirmation', 'AutentikasiController@signupConfirmation');
            Route::post('reset-password', 'AutentikasiController@resetPassword');
            Route::post('reset-password-confirmation', 'AutentikasiController@resetPasswordConfirmation');
            Route::get('all-user', 'AutentikasiController@allUser');
        });
        Route::group(['prefix' => 'confirmation', 'namespace' => 'Autentikasi'], function() {
            Route::post('send', 'ConfirmationController@send');
            Route::post('verify', 'ConfirmationController@verify');
        });
});


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
