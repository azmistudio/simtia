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

Route::group(['middleware' => ['auth']], function() {

    Route::prefix('hr')->group(function() {
        Route::get('/', 'HRController@index')->middleware('permission:data_master-sumber_daya_manusia-index');
        Route::post('/data', 'HRController@data');
        Route::post('/combo-grid', 'HRController@comboGrid');
        Route::post('/store', 'HRController@store')->middleware('permission:data_master-sumber_daya_manusia-store');
        Route::get('/show/{id}', 'HRController@show');
        Route::post('/destroy/{id}', 'HRController@destroy')->middleware('permission:data_master-sumber_daya_manusia-destroy');
        Route::post('/print', 'HRController@print');
        Route::post('/export-excel', 'HRController@toExcel');
        Route::post('/export-pdf', 'HRController@toPdf');
        
        // Profile
        Route::group(['prefix' => 'profile'], function() {
            Route::get('/', 'ProfileController@index');
            Route::post('/data', 'ProfileController@data');
            Route::post('/store/{id}', 'ProfileController@store');
            Route::get('/show/{id}', 'ProfileController@show');
            Route::post('/destroy/{id}', 'ProfileController@destroy');
        });
    });

});
