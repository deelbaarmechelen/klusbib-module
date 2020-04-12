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



\Illuminate\Support\Facades\Log::debug('API routes definition Klusbib');
Route::group(['prefix' => 'v1','namespace' => 'Api', 'middleware' => 'api'], function () {

//    Route::middleware(['apicontext'])->group(['prefix' => 'klusbib'], function () {
    Route::group(['prefix' => 'klusbib', 'middleware' => 'apicontext'], function () {
        Route::get('users',
            [
                'as' => 'api.klusbib.users.index',
                'uses' => 'UsersController@index'
            ]
        );
        Route::get('users/{user_id}',
            [
                'as' => 'api.klusbib.users.show',
                'uses' => 'UsersController@show'
            ]
        );
        Route::put('users/{user_id}',
            [
                'as' => 'api.klusbib.users.update',
                'uses' => 'UsersController@update'
            ]
        );
        Route::get('users/{user_id}/avatar',
            [
                'as' => 'api.avatar.show',
                'uses' => 'AvatarController@show'
            ]
        );

        Route::post('users/{user_id}/avatar',
            [
                'as' => 'api.avatar.create',
//                'uses' => 'AvatarController@create'
                'uses' => 'AvatarController@update'
            ]
        );

        Route::put('users/{user_id}/avatar',
            [
                'as' => 'api.avatar.update',
                'uses' => 'AvatarController@update'
            ]
        );

        Route::post('assets',
            [
                'as' => 'api.klusbib.assets.store',
                'uses' => 'AssetsController@customStore'
            ]
        );

        Route::get('reservations',
            [
                'as' => 'api.klusbib.reservations.index',
                'uses' => 'ReservationsController@index'
            ]
        );
    });
});
// extract from snipe api routes:
//Route::resource('hardware', 'AssetsController',
//    [
//        'names' =>
//            [
//                'index' => 'api.assets.index',
//                'show' => 'api.assets.show',
//                'store' => 'api.assets.store',
//                'update' => 'api.assets.update',
//                'destroy' => 'api.assets.destroy'
//            ],
//        'except' => ['create', 'edit'],
//        'parameters' => ['asset' => 'asset_id']
//    ]
//); // Hardware resource
