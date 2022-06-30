<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReferenceController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\InstituteController;
use App\Http\Controllers\GeneralController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\UpdaterController;

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

Route::get('/', [AuthController::class, 'index'])->name('login');
Route::post('login', [AuthController::class, 'authenticate']);
Route::get('forgot-password', [AuthController::class, 'forgot']);
Route::get('expired', [AuthController::class, 'expired'])->name('expired');
Route::get('file-download/{file}', [HomeController::class, 'fileDownload']);

Route::group(['middleware' => ['auth']], function() {

    Route::get('home', [HomeController::class, 'index'])->name('home');
    Route::get('home/dashboard', [HomeController::class, 'dashboard']);
    Route::get('home/manual', [HomeController::class, 'manual']);
    Route::get('home/about', [HomeController::class, 'about']);
    Route::get('hijri', [HomeController::class, 'getHijri']);
    Route::get('notifications', [HomeController::class, 'notifications']);
    Route::post('logout', [AuthController::class, 'logout']);

    // updater
    Route::group(['prefix' => 'app-updater'], function() {
        Route::get('check', [UpdaterController::class, 'check']);
        Route::get('description', [UpdaterController::class, 'description']);
        // update
        Route::group(['prefix' => 'update'], function() {
            Route::get('/download', [UpdaterController::class, 'updateDownload']);
            Route::get('/extract', [UpdaterController::class, 'updateExtract']);
            Route::get('/install', [UpdaterController::class, 'updateInstall']);
        });   
    });

    // audit log
    Route::group(['prefix' => 'audit'], function() {
        Route::get('log', [AuditLogController::class, 'index']);
        Route::post('log/data', [AuditLogController::class, 'data']);
    });

    // group
    Route::group(['prefix' => 'group'], function() {
        Route::get('/', [AuthController::class, 'group']);
        Route::post('data', [AuthController::class, 'groupData']);
        Route::get('permission', [AuthController::class, 'groupPermission']);
        Route::get('show/{id}', [AuthController::class, 'groupShow']);
        Route::post('store', [AuthController::class, 'groupStore']);
        Route::post('destroy/{id}', [AuthController::class, 'groupDestroy']);
    });

    // user
    Route::group(['prefix' => 'user'], function() {
        Route::get('/', [UserController::class, 'index']);
        Route::post('data', [UserController::class, 'data']);
        Route::post('store', [UserController::class, 'store']);
        Route::get('show/{id}', [UserController::class, 'show']);
        Route::post('destroy/{id}', [UserController::class, 'destroy']);
    });

    // reference
    Route::group(['prefix' => 'reference'], function() {
        Route::get('/', [ReferenceController::class, 'index']);
        Route::post('data', [ReferenceController::class, 'data']);
        Route::post('store/{id}', [ReferenceController::class, 'store']);
        Route::post('list/{param}', [ReferenceController::class, 'list']);
        Route::post('destroy/{id}', [ReferenceController::class, 'destroy']);
    });

    // department
    Route::group(['prefix' => 'department'], function() {
        Route::get('/', [DepartmentController::class, 'index']);
        Route::post('/data', [DepartmentController::class, 'data']);
        Route::post('/store', [DepartmentController::class, 'store']);
        Route::get('/show/{id}', [DepartmentController::class, 'show']);
        Route::post('/destroy/{id}', [DepartmentController::class, 'destroy']);
        Route::post('/export-pdf', [DepartmentController::class, 'toPdf']);
    });    

    // institute
    Route::group(['prefix' => 'institute'], function() {
        Route::get('/', [InstituteController::class, 'index']);
        Route::post('/data', [InstituteController::class, 'data']);
        Route::post('/store', [InstituteController::class, 'store']);
        Route::get('/show/{id}', [InstituteController::class, 'show']);
        Route::post('/destroy/{id}', [InstituteController::class, 'destroy']);
        Route::post('/print', [InstituteController::class, 'print']);
    });   

    // general
    Route::group(['prefix' => 'general'], function() {

        // room
        Route::group(['prefix' => 'room'], function() {
            Route::get('/{subject}', [GeneralController::class, 'indexRoom']);
            Route::post('data', [GeneralController::class, 'dataRoom']);
            Route::post('store', [GeneralController::class, 'storeRoom']);
            Route::get('/show/{id}', [GeneralController::class, 'showRoom']);
            Route::post('destroy/{id}', [GeneralController::class, 'destroyRoom']);
            Route::post('/export-pdf', [GeneralController::class, 'toPdfRoom']);
            Route::post('/combo-grid', [GeneralController::class, 'comboGridRoom']);
        }); 

        // quran
        Route::group(['prefix' => 'quran'], function() {

            // surah
            Route::group(['prefix' => 'surah'], function() {
                Route::get('/combo-box', [GeneralController::class, 'comboBoxSurah']);

            }); 

        }); 

    }); 

}); 