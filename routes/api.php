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

Route::post('/login', 'ApiController@login');
Route::post('/register', 'ApiController@Register');

Route::group(['middleware' => ['auth:api']], function() {

    Route::get('/get-user' , 'ApiController@getUser');
    Route::get('/get-user-profile/{user_no}' , 'ApiController@getThisUser');
    Route::put('/update-profile' , 'ApiController@updateProfile');
    Route::get('/resend-code' , 'ApiController@resendCode');
    Route::post('/confirm-phone' , 'ApiController@confirmCode');

    Route::get('/notification-read/{id}' , 'ApiController@ReadNotification');
    Route::get('/mark-all-notifications-as-read' , 'ApiController@MarkAllAsRead');
    Route::get('/get-unread-notification-counter' , 'ApiController@getUnreadNotificationsCount');
    Route::get('/get-unread-notifications' , 'ApiController@getUnreadNotifications');
    Route::get('/get-all-notifications' , 'ApiController@getAllNotifications');

    Route::get('/get-sms-chart' , 'ApiController@getSmsChart');
    Route::get('/get-sms-cost/{id}' , 'ApiController@getSmsCost');
    Route::get('/get-sms-count' , 'ApiController@getSmsCount');
    Route::post('/buy-sms' , 'ApiController@buySms');

    Route::get('/get-items/{search?}' , 'ApiController@getAllItems');
    Route::get('/get-item/{item_no}' , 'ApiController@getItem');
    Route::get('/get-my-items/{search?}' , 'ApiController@getMyItems');
    Route::post('/add-item' , 'ApiController@addItem');
    Route::put('/update-item/{item_no}' , 'ApiController@updateItem');
    Route::put('/delete-item/{item_no}' , 'ApiController@deleteItem');
    Route::put('/mark-item-unavailable/{item_no}' , 'ApiController@unavailableItem');

});
