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

Route::prefix('klusbib')->group(function() {
//    Route::get('/', 'KlusbibController@index');
    Route::get('/', [
        'as' => 'klusbib.home',
        'uses' => 'KlusbibController@index' ]);
    Route::get('/users', [
        'as' => 'klusbib.users.index',
        'uses' => 'UsersController@index' ]);
    Route::get('/users/create', [
        'as' => 'klusbib.users.create',
        'uses' => 'UsersController@create' ]);
    Route::post('/users', [
        'as' => 'klusbib.users.store',
        'uses' => 'UsersController@store' ]);
    Route::get('/users/{user}/edit', [
        'as' => 'klusbib.users.edit',
        'uses' => 'UsersController@edit' ]);
    Route::put('/users/{user}', [
        'as' => 'klusbib.users.update',
        'uses' => 'UsersController@update' ]);
    Route::get('/users/{user}', [
        'as' => 'klusbib.users.show',
        'uses' => 'UsersController@show' ]);
    Route::get('/reservations', [
        'as' => 'klusbib.reservations.index',
        'uses' => 'ReservationsController@index' ]);
    Route::get('/reservations/{reservation}/cancel', [
        'as' => 'klusbib.reservations.cancel',
        'uses' => 'ReservationsController@cancel' ]);
    Route::get('/reservations/{reservation}/confirm', [
        'as' => 'klusbib.reservations.confirm',
        'uses' => 'ReservationsController@confirm' ]);
    Route::get('/reservations/export', [
        'as' => 'klusbib.reservations.export',
        'uses' => 'ReservationsController@export' ]);
    Route::get('/deliveries', [
        'as' => 'klusbib.deliveries.index',
        'uses' => 'DeliveriesController@index' ]);
    Route::get('/memberships', [
        'as' => 'klusbib.memberships.index',
        'uses' => 'MembershipsController@index' ]);
    Route::get('/payments', [
        'as' => 'klusbib.payments.index',
        'uses' => 'PaymentsController@index' ]);
    Route::get('/lendings', [
        'as' => 'klusbib.lendings.index',
        'uses' => 'LendingsController@index' ]);
});
Route::resource('klusbib/reservations', 'ReservationsController', [
    'middleware' => ['auth'],
    'parameters' => ['reservation' => 'reservation_id'],
])->names([
    'index' => 'klusbib.reservations.index',
    'create' => 'klusbib.reservations.create',
    'store' => 'klusbib.reservations.store',
    'show' => 'klusbib.reservations.show',
    'edit' => 'klusbib.reservations.edit',
    'update' => 'klusbib.reservations.update',
    'destroy' => 'klusbib.reservations.destroy',
]);

/*
 * Resource actions (see https://laravel.com/docs/7.x/controllers#resource-controllers)
 * Verb 	URI 	        Action 	    Route Name
 * GET 	    /photos 	    index 	    photos.index
 * GET 	    /photos/create 	create 	    photos.create
 * POST 	/photos 	    store 	    photos.store
 * GET 	    /photos/{photo} show 	    photos.show
 * GET 	    /photos/{photo}/edit edit 	photos.edit
 * PUT/PATCH /photos/{photo}update 	    photos.update
 * DELETE 	/photos/{photo} destroy 	photos.destroy
 */
Route::group(
    ['prefix' => 'users',
        'middleware' => ['auth']],
    function () {
        // override user edit
        Route::get('{user}/edit', [
            'as' => 'users.edit',
            'uses' => '\Modules\Klusbib\Http\Controllers\UsersController@edit'
            //            'uses' => 'AssetsController@dueForAudit'
        ]);
    });