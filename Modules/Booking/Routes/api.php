<?php

use Illuminate\Support\Facades\Route;

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
Route::prefix('booking')->group(function () {

    Route::group(['prefix' => 'units'], function () {
        Route::controller(\Modules\Booking\Http\Controllers\UnitController::class)->group(function () {
            Route::get('/get-drop-down', 'getDropDown')->name('units.getDropDown');
            Route::get('/', 'all')->name('units.index');
            Route::get('/{id}', 'find')->name('units.find');
            Route::post('/', 'create')->name('units.create');
            Route::put('/{id}', 'update')->name('units.update');
            Route::delete('/{id}', 'delete')->name('units.destroy');
            Route::post("/bulk-delete", "bulkDelete");
            Route::get('logs/{id}', 'logs')->name('units.logs');
        });
    });
    Route::group(['prefix' => 'Settings'], function () {
        Route::controller(\Modules\Booking\Http\Controllers\SettingController::class)->group(function () {
            Route::get('/', 'all')->name('Settings.index');
            Route::put('/update', 'update')->name('Settings.update');

        });
    });

});
