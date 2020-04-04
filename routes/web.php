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
    return redirect('/login');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/user/info', 'UserController@getUserInfo')->name('user.info');
Route::get('/user/payment', 'User\PaymentController@getCurrentPayment')->name('user.payment');
Route::get('/user/payment/form', 'User\PaymentController@getPaymentForm')->name('user.payment.form');
Route::post('/user/payment/store', 'User\PaymentController@storePaymentInfo')->name('user.payment.store');
Route::post('/user/payment/destroy', 'User\PaymentController@deletePaymentInfo')->name('user.payment.destroy');
