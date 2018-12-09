<?php

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

Route::get('/', 'PagesController@root')->name('root');

Auth::routes();

Route::group(['middleware' => 'auth'], function() {
    Route::get('/email_verify_notice', 'PagesController@emailVerifyNotice')->name('email_verify_notice');
    Route::get('/email_verification/verify', 'EmailVerificationController@verify')->name('email_verification.verify');
    // 用户主动请求发送激活邮件
    Route::get('/email_verification/send', 'EmailVerificationController@send')->name('email_verification.send');
    // 验证过邮箱才能使用以下路由
    Route::group(['middleware' => 'email_verified'], function() {
        Route::get('user-addresses', 'UserAddressesController@index')->name('user_addresses.index');
        Route::get('user-addresses/create', 'UserAddressesController@create')->name('user_addresses.create');
        Route::post('user-addresses', 'UserAddressesController@store')->name('user_addresses.store');
        Route::get('user-addresses/{user_address}', 'UserAddressesController@edit')->name('user_addresses.edit');
        Route::put('user-addresses/{user_address}', 'UserAddressesController@update')->name('user_addresses.update');
        Route::delete('user-addresses/{user_address}', 'UserAddressesController@destroy')->name('user_addresses.destroy');
    });

});