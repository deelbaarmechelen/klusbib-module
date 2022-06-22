<?php

/*
|--------------------------------------------------------------------------
| Web Routes Overrides
|--------------------------------------------------------------------------
|
| Here is where you can register web routes to override defaults of application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(
    ['prefix' => 'hardware',
        'middleware' => ['auth']],
    function () {
        // Sample override
        Route::get('audit/due', [
            'as' => 'assets.audit.due',
            'uses' => 'KlusbibController@index'
//            'uses' => 'AssetsController@dueForAudit'
        ]);
        // Sample override referring to controller in main app
//        Route::get('audit/overdue', [
//            'as' => 'assets.audit.overdue',
//            'uses' => '\App\Http\Controllers\AssetsController@overdueForAudit'
//        ]);

//Route::get('{assetId}/qr_code', [ 'as' => 'qr_code/hardware', 'uses' => 'AssetsController@getQrCode' ]);
//Route::get('{assetId}/barcode', [ 'as' => 'barcode/hardware', 'uses' => 'AssetsController@getBarCode' ]);
    });

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

//Route::get('barcodes', ['as' => 'settings.barcodes.index','uses' => 'SettingsController@getBarcodes' ]);
//Route::post('barcodes', ['as' => 'settings.barcodes.save','uses' => 'SettingsController@postBarcodes' ]);

Route::group(
    ['prefix' => 'hardware',
        'middleware' => ['auth']],
    function () {

//Route::get('{assetId}/qr_code', [ 'as' => 'qr_code/hardware', 'uses' => 'AssetsController@getQrCode' ]);
//Route::get('{assetId}/barcode', [ 'as' => 'barcode/hardware', 'uses' => 'AssetsController@getBarCode' ]);

    });