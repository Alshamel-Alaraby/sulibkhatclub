<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\HR\Http\Controllers\PayrollHeadController;


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

Route::prefix('hr')->group(function () {


    Route::group(['prefix' => 'payroll-heads'], function () {
        Route::get('/', 'PayrollHeadController@all')->name('payroll-heads.all');
        Route::get('/logs/{id}', 'PayrollHeadController@logs')->name('payroll-heads.logs');
        Route::get('/{id}', 'PayrollHeadController@find')->name('payroll-heads.find');
        Route::post('/', 'PayrollHeadController@create')->name('payroll-heads.create');
        Route::put('/{id}', 'PayrollHeadController@update')->name('payroll-heads.update');
        Route::post("/bulk-delete", "PayrollHeadController@bulkDelete");
        Route::delete('/{id}', 'PayrollHeadController@delete')->name('payroll-heads.delete');
        Route::post('/sync','PayrollHeadController@processJsonData');
    });

    Route::group(['prefix' => 'job-title'], function () {
        Route::controller(\Modules\HR\Http\Controllers\JobTitleController::class)->group(function () {
            Route::get('/', 'all')->name('job-title.index');
            Route::get('/{id}', 'find')->name('job-title.find');
            Route::post('/', 'create')->name('job-title.create');
            Route::put('/{id}', 'update')->name('job-title.update');
            Route::delete('/{id}', 'delete')->name('job-title.destroy');
            Route::post("/bulk-delete", "bulkDelete");
            Route::get('logs/{id}', 'logs')->name('job-title.logs');
            Route::post('/sync','processJsonData');
        });
    });

    // request types routes
    Route::group(['prefix' => 'request-types'], function () {
        Route::controller(\Modules\HR\Http\Controllers\RequestTypeController::class)->group(function () {
            Route::get('/', 'all')->name('request-types.index');
            Route::get('/{id}', 'find')->name('request-types.find');
            Route::post('/', 'create')->name('request-types.create');
            Route::put('/{id}', 'update')->name('request-types.update');
            Route::delete('/{id}', 'delete')->name('request-types.destroy');
            Route::post("bulk-delete", "bulkDelete");
            Route::get('logs/{id}', 'logs')->name('request-types.logs');
        });
    });

    // online requests routes
    Route::group(['prefix' => 'online-requests'], function () {
        Route::controller(\Modules\HR\Http\Controllers\OnlineRequestController::class)->group(function () {
            Route::get('/', 'all')->name('online-requests.index');
            Route::get('/{id}', 'find')->name('online-requests.find');
            Route::post('/', 'create')->name('online-requests.create');
            Route::put('/{id}', 'update')->name('online-requests.update');
            Route::delete('/{id}', 'delete')->name('online-requests.destroy');
            Route::post("bulk-delete", "bulkDelete");
            Route::get('logs/{id}', 'logs')->name('online-requests.logs');
        });
    });

});
