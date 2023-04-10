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

Route::group(["prefix" => "cus"], function () {

    // types routes
    Route::group(["prefix" => "types"], function () {
        Route::get("/", "TypeController@all");
        Route::get("/logs/{id}", "TypeController@logs");
        Route::get("/{id}", "TypeController@find");
        Route::post("/", "TypeController@create");
        Route::put("/{id}", "TypeController@update");
        Route::delete("/bulk", "TypeController@bulkDelete");
        Route::delete("/{id}", "TypeController@delete");

    });

    // items routes

    Route::group(["prefix" => "items"], function () {
        Route::get("/", "ItemController@all");
        Route::get("/logs/{id}", "ItemController@logs");
        Route::get("/{id}", "ItemController@find");
        Route::post("/", "ItemController@create");
        Route::put("/{id}", "ItemController@update");
        Route::delete("/bulk", "ItemController@bulkDelete");
        Route::delete("/{id}", "ItemController@delete");

    });

});
