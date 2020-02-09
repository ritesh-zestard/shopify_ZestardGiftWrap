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


// Callback route

Route::get('dashboard', 'callbackController@dashboard')->name('dashboard');
Route::get('callback', 'callbackController@index')->name('callback');
Route::get('redirect', 'callbackController@redirect')->name('redirect');
Route::any('change_currency', 'callbackController@Currency')->name('change_currency');
Route::get('uninstall', 'callbackController@uninstall')->name('uninstall');
Route::get('payment_process', 'callbackController@payment_method')->name('payment_process');
Route::get('payment_success', 'callbackController@payment_compelete')->name('payment_success');
Route::get('declined', 'callbackController@declined')->name('declined');
Route::any('update-modal-status', 'callbackController@update_modal_status')->name('update-modal-status');

// Backed route

Route::get('gift_wrap', 'BackendController@index')->name('gift_wrap');
Route::get('gift_wrap_image', 'BackendController@giftimage')->name('gift_wrap_image');
Route::any('webhook', 'BackendController@webhook')->name('webhook');
Route::post('giftwrap_save', 'BackendController@store')->name('giftwrap_save');

// Frontend controller route

Route::group(['middleware' => ['cors']], function () {
    Route::get('preview', 'FrontController@index')->name('preview');
    Route::get('front_preview', 'FrontController@frontView')->name('front_preview');
    Route::get('giftwrap_image', 'FrontController@giftWrapImage')->name('giftwrap_image');
    Route::post('frontend/add_giftwarp', 'FrontController@checkgiftwrap')->name('frontend/add_giftwarp');
});

Route::get('help', function () {
    return view('help');
})->name('help');

Route::get('decline', function () {
    return view('decline');
})->name('decline');

Route::post('snippet-create-product', 'callbackController@SnippetCreateProduct')->name('snippet_create_product');

Route::post('snippet-create-cart', 'callbackController@SnippetCreateCart')->name('snippet_create_cart');