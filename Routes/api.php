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

Route::middleware('auth:api')->get('/klusbib', function (Request $request) {
    return $request->user();
});




Route::group(['prefix' => 'v1','namespace' => 'Api', 'middleware' => 'api'], function () {

    Route::group(['prefix' => 'klusbib'], function () {
        Route::get('users',
            [
                'as' => 'api.klusbib.users.index',
                'uses' => 'UsersController@index'
            ]
        );
        Route::get('users/avatar',
            [
                'as' => 'api.avatar.show',
                'uses' => 'AvatarController@show'
            ]
        );

        Route::post('users/avatar',
            [
                'as' => 'api.avatar.create',
//                'uses' => 'AvatarController@create'
                'uses' => 'AvatarController@update'
            ]
        );

        Route::put('users/avatar/{user_id}',
            [
                'as' => 'api.avatar.update',
                'uses' => 'AvatarController@update'
            ]
        );
    });
});
