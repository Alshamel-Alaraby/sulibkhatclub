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
Route::prefix('boards-rent')->group(function () {

    // departments routes
    Route::group(['prefix' => 'departments'], function () {
        Route::get('/', 'DepartmentController@all')->name('boards-rent.departments.all');
        Route::post('/', 'DepartmentController@create')->name('boards-rent.departments.create');
        Route::put('/{id}', 'DepartmentController@update')->name('boards-rent.departments.update');
        Route::delete("/bulk-delete", "DepartmentController@bulkDelete");
        Route::get('/logs/{id}', 'DepartmentController@logs')->name('boards-rent.departments.logs');
        Route::get('/{id}', 'DepartmentController@find')->name('boards-rent.departments.find');
        Route::delete('/{id}', 'DepartmentController@delete')->name('boards-rent.departments.delete');
    });

    // sell methods routes
    Route::group(['prefix' => 'sell-methods'], function () {
        Route::get('/', 'SellMethodController@all')->name('boards-rent.sell-methods.all');
        Route::post('/', 'SellMethodController@create')->name('boards-rent.sell-methods.create');
        Route::put('/{id}', 'SellMethodController@update')->name('boards-rent.sell-methods.update');
        Route::delete("/bulk-delete", "SellMethodController@bulkDelete");
        Route::get('/logs/{id}', 'SellMethodController@logs')->name('boards-rent.sell-methods.logs');
        Route::get('/{id}', 'SellMethodController@find')->name('boards-rent.sell-methods.find');
        Route::delete('/{id}', 'SellMethodController@delete')->name('boards-rent.sell-methods.delete');
    });

    //  sectors routes
    Route::group(['prefix' => 'sectors'], function () {
        Route::get('/', 'SectorController@all')->name('boards-rent.sectors.all');
        Route::post('/', 'SectorController@create')->name('boards-rent.sectors.create');
        Route::put('/{id}', 'SectorController@update')->name('boards-rent.sectors.update');
        Route::delete("/bulk-delete", "SectorController@bulkDelete");
        Route::get('/logs/{id}', 'SectorController@logs')->name('boards-rent.sectors.logs');
        Route::get('/{id}', 'SectorController@find')->name('boards-rent.sectors.find');
        Route::delete('/{id}', 'SectorController@delete')->name('boards-rent.sectors.delete');
    });

    // customer sources
    Route::group(['prefix' => 'customer-sources'], function () {
        Route::get('/', 'CustomerSourceController@all')->name('boards-rent.customer-sources.all');
        Route::post('/', 'CustomerSourceController@create')->name('boards-rent.customer-sources.create');
        Route::put('/{id}', 'CustomerSourceController@update')->name('boards-rent.customer-sources.update');
        Route::delete("/bulk-delete", "CustomerSourceController@bulkDelete");
        Route::get('/logs/{id}', 'CustomerSourceController@logs')->name('boards-rent.customer-sources.logs');
        Route::get('/{id}', 'CustomerSourceController@find')->name('boards-rent.customer-sources.find');
        Route::delete('/{id}', 'CustomerSourceController@delete')->name('boards-rent.customer-sources.delete');
    });

    // Package
    Route::group(['prefix' => 'packages'], function () {
        Route::get('/', 'PackageController@all')->name('boards-rent.packages.all');
        Route::post('/', 'PackageController@create')->name('boards-rent.packages.create');
        Route::put('/{id}', 'PackageController@update')->name('boards-rent.packages.update');
        Route::delete("/bulk-delete", "PackageController@bulkDelete");
        Route::get('/logs/{id}', 'PackageController@logs')->name('boards-rent.packages.logs');
        Route::get('/{id}', 'PackageController@find')->name('boards-rent.packages.find');
        Route::delete('/{id}', 'PackageController@delete')->name('boards-rent.packages.delete');
    });

    // Unit Status
    Route::group(['prefix' => 'unit_statuses'], function () {
        Route::get('/', 'UnitStatusController@all')->name('boards-rent.unit_statuses.all');
        Route::post('/', 'UnitStatusController@create')->name('boards-rent.unit_statuses.create');
        Route::put('/{id}', 'UnitStatusController@update')->name('boards-rent.unit_statuses.update');
        Route::delete("/bulk-delete", "UnitStatusController@bulkDelete");
        Route::get('/logs/{id}', 'UnitStatusController@logs')->name('boards-rent.unit_statuses.logs');
        Route::get('/{id}', 'UnitStatusController@find')->name('boards-rent.unit_statuses.find');
        Route::delete('/{id}', 'UnitStatusController@delete')->name('boards-rent.unit_statuses.delete');
    });

    //Panels
    Route::group(['prefix' => 'panels'], function () {
        Route::get('/', 'PanelController@all')->name('boards-rent.panels.all');
        Route::post('/', 'PanelController@create')->name('boards-rent.panels.create');
        Route::put('/{id}', 'PanelController@update')->name('boards-rent.panels.update');
        Route::delete("/bulk-delete", "PanelController@bulkDelete");
        Route::get('/logs/{id}', 'PanelController@logs')->name('boards-rent.panels.logs');
        Route::get('/{id}', 'PanelController@find')->name('boards-rent.panels.find');
        Route::delete('/{id}', 'PanelController@delete')->name('boards-rent.panels.delete');
    });

    //Tasks
    Route::group(['prefix' => 'tasks'], function () {
        Route::get('/', 'TaskController@all')->name('boards-rent.tasks.all');
        Route::post('/', 'TaskController@create')->name('boards-rent.tasks.create');
        Route::put('/{id}', 'TaskController@update')->name('boards-rent.tasks.update');
        Route::delete("/bulk-delete", "TaskController@bulkDelete");
        Route::get('/logs/{id}', 'TaskController@logs')->name('boards-rent.tasks.logs');
        Route::get('/{id}', 'TaskController@find')->name('boards-rent.tasks.find');
        Route::delete('/{id}', 'TaskController@delete')->name('boards-rent.tasks.delete');
    });

    //LookUp
    Route::group(['prefix' => 'look_ups'], function () {
        Route::get('root-nodes', 'LookUpController@getRootNodes');
        Route::get('/child-nodes/{parentId}', 'LookUpController@getChildNodes');
        Route::get('/', 'LookUpController@all')->name('boards-rent.look_ups.all');
        Route::post('/', 'LookUpController@create')->name('boards-rent.look_ups.create');
        Route::put('/{id}', 'LookUpController@update')->name('boards-rent.look_ups.update');
        Route::delete("/bulk-delete", "LookUpController@bulkDelete");
        Route::get('/logs/{id}', 'LookUpController@logs')->name('boards-rent.look_ups.logs');
        Route::get('/{id}', 'LookUpController@find')->name('boards-rent.look_ups.find');
        Route::delete('/{id}', 'LookUpController@delete')->name('boards-rent.look_ups.delete');
    });

    // orders
    Route::group(['prefix' => 'orders'], function () {
        Route::get('/', 'OrderController@all')->name('boards-rent.orders.all');
        Route::post('/', 'OrderController@create')->name('boards-rent.orders.create');
        Route::put('/{id}', 'OrderController@update')->name('boards-rent.orders.update');
        Route::delete("/bulk-delete", "OrderController@bulkDelete");
        Route::get('/logs/{id}', 'OrderController@logs')->name('boards-rent.orders.logs');
        Route::get('/{id}', 'OrderController@find')->name('boards-rent.orders.find');
        Route::delete('/{id}', 'OrderController@delete')->name('boards-rent.orders.delete');
    });

});

// customers
Route::group(['prefix' => 'general-customer'], function () {
    Route::get('/', 'CustomerController@all')->name('boards-rent.customers.all');
    Route::post('/', 'CustomerController@create')->name('boards-rent.customers.create');
    Route::put('/{id}', 'CustomerController@update')->name('boards-rent.customers.update');
    Route::delete("/bulk-delete", "CustomerController@bulkDelete");
    Route::get('/logs/{id}', 'CustomerController@logs')->name('boards-rent.customers.logs');
    Route::get('/{id}', 'CustomerController@find')->name('boards-rent.customers.find');
    Route::delete('/{id}', 'CustomerController@delete')->name('boards-rent.customers.delete');
});