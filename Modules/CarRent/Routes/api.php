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

Route::prefix('car-rent')->group(function () {

    Route::group(['prefix' => 'car-models'], function () {
        Route::controller(\Modules\CarRent\Http\Controllers\CarModelController::class)->group(function () {
            Route::get('/', 'all')->name('car-models.index');
            Route::get('/{id}', 'find')->name('car-models.find');
            Route::get('/car-model/{id}', 'carModel')->name('car-models.carModel');
            Route::post('/', 'create')->name('car-models.create');
            Route::put('/{id}', 'update')->name('car-models.update');
            Route::delete('/{id}', 'delete')->name('car-models.destroy');
            Route::post("/bulk-delete", "bulkDelete");
            Route::get('logs/{id}', 'logs')->name('car-models.logs');
        });
    });
    Route::group(['prefix' => 'car-transmissions'], function () {
        Route::controller(\Modules\CarRent\Http\Controllers\CarTransmissionController::class)->group(function () {
            Route::get('/', 'all')->name('car-transmissions.index');
            Route::get('/{id}', 'find')->name('car-transmissions.find');
            Route::post('/', 'create')->name('car-transmissions.create');
            Route::put('/{id}', 'update')->name('car-transmissions.update');
            Route::delete('/{id}', 'delete')->name('car-transmissions.destroy');
            Route::post("/bulk-delete", "bulkDelete");
            Route::get('logs/{id}', 'logs')->name('car-transmissions.logs');
        });
    });
    Route::group(['prefix' => 'car-sunroofs'], function () {
        Route::controller(\Modules\CarRent\Http\Controllers\CarSunroofController::class)->group(function () {
            Route::get('/', 'all')->name('car-sunroofs.index');
            Route::get('/{id}', 'find')->name('car-sunroofs.find');
            Route::post('/', 'create')->name('car-sunroofs.create');
            Route::put('/{id}', 'update')->name('car-sunroofs.update');
            Route::delete('/{id}', 'delete')->name('car-sunroofs.destroy');
            Route::post("/bulk-delete", "bulkDelete");
            Route::get('logs/{id}', 'logs')->name('car-sunroofs.logs');
        });
    });
    Route::group(['prefix' => 'car-body-types'], function () {
        Route::controller(\Modules\CarRent\Http\Controllers\CarBodyTypeController::class)->group(function () {
            Route::get('/', 'all')->name('car-body-types.index');
            Route::get('/{id}', 'find')->name('car-body-types.find');
            Route::post('/', 'create')->name('car-body-types.create');
            Route::put('/{id}', 'update')->name('car-body-types.update');
            Route::delete('/{id}', 'delete')->name('car-body-types.destroy');
            Route::post("/bulk-delete", "bulkDelete");
            Route::get('logs/{id}', 'logs')->name('car-body-types.logs');
        });
    });
    Route::group(['prefix' => 'car-fuel-types'], function () {
        Route::controller(\Modules\CarRent\Http\Controllers\CarFuelTypeController::class)->group(function () {
            Route::get('/', 'all')->name('car-fuel-types.index');
            Route::get('/{id}', 'find')->name('car-fuel-types.find');
            Route::post('/', 'create')->name('car-fuel-types.create');
            Route::put('/{id}', 'update')->name('car-fuel-types.update');
            Route::delete('/{id}', 'delete')->name('car-fuel-types.destroy');
            Route::post("/bulk-delete", "bulkDelete");
            Route::get('logs/{id}', 'logs')->name('car-fuel-types.logs');
        });
    });
    Route::group(['prefix' => 'car-seats-material'], function () {
        Route::controller(\Modules\CarRent\Http\Controllers\CarSeatsMaterialController::class)->group(function () {
            Route::get('/', 'all')->name('car-seats-material.index');
            Route::get('/{id}', 'find')->name('car-seats-material.find');
            Route::post('/', 'create')->name('car-seats-material.create');
            Route::put('/{id}', 'update')->name('car-seats-material.update');
            Route::delete('/{id}', 'delete')->name('car-seats-material.destroy');
            Route::post("/bulk-delete", "bulkDelete");
            Route::get('logs/{id}', 'logs')->name('car-seats-material.logs');
        });
    });

    Route::group(['prefix' => 'car-specifications'], function () {
        Route::controller(\Modules\CarRent\Http\Controllers\CarSpecificationController::class)->group(function () {
            Route::get('/', 'all')->name('car-specifications.index');
            Route::get('/{id}', 'find')->name('car-specifications.find');
            Route::post('/', 'create')->name('car-specifications.create');
            Route::put('/{id}', 'update')->name('car-specifications.update');
            Route::delete('/{id}', 'delete')->name('car-specifications.destroy');
            Route::post("/bulk-delete", "bulkDelete");
            Route::get('logs/{id}', 'logs')->name('car-specifications.logs');
        });
    });

    Route::group(['prefix' => 'car-cars'], function () {
        Route::controller(\Modules\CarRent\Http\Controllers\CarCarController::class)->group(function () {
            Route::get('/', 'all')->name('car-cars.index');
            Route::get('/{id}', 'find')->name('car-cars.find');
            Route::post('/', 'create')->name('car-cars.create');
            Route::put('/{id}', 'update')->name('car-cars.update');
            Route::post('/update', 'updatePrices');
            Route::delete('/{id}', 'delete')->name('car-cars.destroy');
            Route::post("/bulk-delete", "bulkDelete");
            Route::get('logs/{id}', 'logs')->name('car-cars.logs');
        });
    });

    Route::group(['prefix' => 'car-drivers'], function () {
        Route::controller(\Modules\CarRent\Http\Controllers\CarDriverController::class)->group(function () {
            Route::get('/', 'all')->name('car-drivers.index');
            Route::get('/{id}', 'find')->name('car-drivers.find');
            Route::post('/', 'create')->name('car-drivers.create');
            Route::put('/{id}', 'update')->name('car-drivers.update');
            Route::delete('/{id}', 'delete')->name('car-drivers.destroy');
            Route::post("/bulk-delete", "bulkDelete");
            Route::get('logs/{id}', 'logs')->name('car-drivers.logs');
        });
    });


});
