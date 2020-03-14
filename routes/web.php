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
use Illuminate\Http\Request;
use SampleProject\User;


Route::group(['middleware' => ['web', 'guest']], function () {
    Route::get('/', function(){
        return view('index');
    });

    Route::get('/check-mobile-no/{no}', function( $no){
        $check_mobile_no = User::where('mobile_no','like', '%'.$no.'%')->first();
        if (!$check_mobile_no){
            return response()->json('yes', 200);
        }else {
            return response()->json('no', 200);
        }
    });


});


Route::group(['middleware' => 'web'], function () {
    Route::auth();
    // Auth::routes();

    Route::post('beyonic-callback/response','CallbackController@index');

    //all routes for admin
    Route::group(['prefix' => 'admin'], function () {
        Route::resource('/', 'AdminController');

    });
    //all routes for admin
    Route::group(['prefix' => 'client'], function () {
        Route::resource('/', 'ClientController');
        Route::get('/profile' , 'ClientController@profile');
        Route::get('/view-profile/{user_no}/{name}' , 'ClientController@getUserProfile');
        Route::get('/get-user-profile/{user_no}' , 'ClientController@getThisUser');
        Route::put('/update-info' , 'ClientController@updateProfile');
        Route::get('/get-user' , 'ClientController@getUser');
        Route::get('/resend-code' , 'ClientController@resendCode');
        Route::post('/confirm-phone' , 'ClientController@confirmCode');
        Route::get('/notifications' , 'ClientController@showNotifications');
        Route::get('/notification-read/{id}/{user_id}/{name}' , 'ClientController@ReadNotification');
        Route::get('/mark-all-notifications-as-read' , 'ClientController@MarkAllAsRead');
        Route::get('/get-notifications' , 'ClientController@getNotifications');
        Route::get('/get-sms-chart' , 'ClientController@getSmsCostChart');
        Route::get('/get-sms-cost/{id}' , 'ClientController@getSmsCost');
        Route::get('/get-sms' , 'ClientController@getSmsCount');
        Route::post('/buy-sms' , 'ClientController@buySms');
        Route::get('/my-items' , 'ClientController@myItems');
        Route::get('/get-items/{search?}' , 'ClientController@getAllItems');
        Route::get('/get-my-items/{search?}' , 'ClientController@getMyItems');
        Route::post('/add-item' , 'ClientController@addItem');
        Route::put('/update-item/{id}' , 'ClientController@updateItem');
        Route::post('/delete-item/{id}' , 'ClientController@deleteItem');
        Route::put('/mark-item-unavailable/{item_no}' , 'ApiController@unavailableItem');




    });
});
