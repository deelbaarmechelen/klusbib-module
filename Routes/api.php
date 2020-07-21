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

        Route::group(['prefix' => 'sync', 'middleware' => 'apicontext'], function () {
            // reserved for syncing requests from Klusbib API
            Route::post('users',
                [
                    'as' => 'api.klusbib.sync.users.new',
                    'uses' => 'UsersController@syncNew'
                ]
            );
            Route::put('users/{user_id}',
                [
                    'as' => 'api.klusbib.sync.users.updt',
                    'uses' => 'UsersController@syncUpdate'
                ]
            );
            Route::delete('users/{user_id}',
                [
                    'as' => 'api.klusbib.sync.users.del',
                    'uses' => 'UsersController@syncDelete'
                ]
            );

        });

        Route::get('users',
            [
                'as' => 'api.klusbib.users.index',
                'uses' => 'UsersController@index'
            ]
        );
        Route::get('users/selectlist',
            [
                'as' => 'api.klusbib.users.selectlist',
                'uses' => 'UsersController@selectlist'
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
        Route::get('lendings',
            [
                'as' => 'api.klusbib.lendings.index',
                'uses' => 'LendingsController@index'
            ]
        );
        Route::get('lendings/byduedate',
            [
                'as' => 'api.klusbib.lendings.byduedate',
                'uses' => 'LendingsController@getLendingsByDueDate'
            ]
        );
        Route::get('lendings/bycategory',
            [
                'as' => 'api.klusbib.lendings.bycategory',
                'uses' => 'LendingsController@getLendingsByCategory'
            ]
        );
        Route::get('stats/usersbyproject',
            [
                'as' => 'api.klusbib.stats.users.byproject',
                'uses' => 'StatsController@getUsersCountByProject'
            ]
        );
        Route::get('stats/activitybyproject',
            [
                'as' => 'api.klusbib.stats.activity.byproject',
                'uses' => 'StatsController@getActivityCountByProject'
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
