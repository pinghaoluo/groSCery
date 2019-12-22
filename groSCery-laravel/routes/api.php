<?php

use Illuminate\Http\Request;

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

Route::post('login', 'UserController@login');
Route::post('register', 'UserController@register');
Route::group(['middleware' => 'auth:api'], function(){

    // Users routes
    Route::post('logout', 'UserController@logout');
    Route::get('users/details', 'UserController@details');
    Route::get('users/items','UserController@items');

    // Groups routes
    Route::get('groups/list','GroupController@list');
    Route::get('groups/items','GroupController@items');
    Route::get('groups/subscribers', 'GroupController@subscribers');
    Route::post('groups/create','GroupController@create');
    Route::post('groups/subscribe/{group_id}','GroupController@subscribe');
    Route::post('groups/unsubscribe','GroupController@unsubscribe');

    // Items routes
    Route::post('items/create', 'ItemController@create');
    Route::get('items/subscribers/{item_id}', 'ItemController@subscribers');
    Route::post('items/subscribe/{item_id}', 'ItemController@subscribe');
    Route::post('items/unsubscribe/{item_id}', 'ItemController@unsubscribe');
    Route::post('items/in-stock/{item_id}','ItemController@inStock');
    Route::post('items/out-of-stock/{item_id}','ItemController@outOfStock');

    // Transaction routes
    Route::post('transactions/purchase','TransactionController@purchase');
});