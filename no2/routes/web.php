<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('layouts.dashboard');
});

Route::get('/verify-account/{id}', 'VerifikasiAkunController@addPassword');
Route::post('/store-password', 'VerifikasiAkunController@storePassword')->name('store-password');


Route::group(['prefix'=>'errors'],function(){
    Route::get('error500','Helpers\WebHelperController@error500')->name('error.500');
    Route::get('error404','Helpers\WebHelperController@error404')->name('error.404');
});
Route::get('index-logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index')->name('log');
Route::match(['get', 'post'],'custom-register', 'Auth\RegisterController@customRegister')->name('custom-register');
Route::get('regis-confirmation', 'Auth\RegisterController@signupConfirmation')->name('regis-confirmation');


Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
