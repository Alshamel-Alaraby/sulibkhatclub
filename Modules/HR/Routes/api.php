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

Route::prefix('hr')->group(function () {

    Route::group(['prefix' => 'payroll-heads'], function () {
        Route::get('/', 'PayrollHeadController@all')->name('payroll-heads.all');
        Route::get('/logs/{id}', 'PayrollHeadController@logs')->name('payroll-heads.logs');
        Route::get('/{id}', 'PayrollHeadController@find')->name('payroll-heads.find');
        Route::post('/', 'PayrollHeadController@create')->name('payroll-heads.create');
        Route::put('/{id}', 'PayrollHeadController@update')->name('payroll-heads.update');
        Route::post("/bulk-delete", "PayrollHeadController@bulkDelete");
        Route::delete('/{id}', 'PayrollHeadController@delete')->name('payroll-heads.delete');
        Route::post('/sync', 'PayrollHeadController@processJsonData');
    });

    Route::group(['prefix' => 'job-title'], function () {
        Route::controller(\Modules\HR\Http\Controllers\JobTitleController::class)->group(function () {
            Route::get('/get-drop-down', 'getDropDown')->name('job-title.getDropDown');
            Route::get('/', 'all')->name('job-title.index');
            Route::get('/{id}', 'find')->name('job-title.find');
            Route::post('/', 'create')->name('job-title.create');
            Route::put('/{id}', 'update')->name('job-title.update');
            Route::delete('/{id}', 'delete')->name('job-title.destroy');
            Route::post("/bulk-delete", "bulkDelete");
            Route::get('logs/{id}', 'logs')->name('job-title.logs');
            Route::post('/sync', 'processJsonData');
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

    // hr routes
    Route::group(['prefix' => 'statues'], function () {
        Route::controller(\Modules\HR\Http\Controllers\StatusController::class)->group(function () {
            Route::get('/', 'all')->name('statues.index');
            Route::get('/{id}', 'find')->name('statues.find');
            Route::post('/', 'create')->name('statues.create');
            Route::put('/{id}', 'update')->name('statues.update');
            Route::delete('/{id}', 'delete')->name('statues.destroy');
            Route::post("bulk-delete", "bulkDelete");
            Route::get('logs/{id}', 'logs')->name('statues.logs');
        });
    });

    // end-service routes
    Route::group(['prefix' => 'end-service'], function () {
        Route::controller(\Modules\HR\Http\Controllers\EndServiceController::class)->group(function () {
            Route::get('/getAll', 'all');
            Route::post('/getEmployee', 'getEmployee');
            Route::post('/', 'create');
            Route::post('/sync', 'processJsonData');
        });
    });

    // vacation-balance routes
    Route::group(['prefix' => 'vacation-balance'], function () {
        Route::controller(\Modules\HR\Http\Controllers\VacationBalanceController::class)->group(function () {
            Route::get('/getAll', 'all');
            Route::post('/getEmployee', 'getEmployee');
            Route::post('/store', 'store');
            Route::post('/sync', 'processJsonData');
        }); ////////////////////////////////////////
    });

    // payroll routes
    Route::group(['prefix' => 'payroll'], function () {
        Route::controller(\Modules\HR\Http\Controllers\PayrollController::class)->group(function () {
            Route::get('/getAll', 'all');
            Route::post('/getEmployee', 'getEmployee');
            Route::post('/store', 'store');
            Route::post('/sync', 'processJsonData');
        });
    });

    // Emergency-balance routes
    Route::group(['prefix' => 'Emergency-balance'], function () {
        Route::controller(\Modules\HR\Http\Controllers\EmergencyBalanceController::class)->group(function () {
            Route::get('/getAll', 'all');
            Route::post('/getEmployee', 'getEmployee');
            Route::post('/store', 'store');
            Route::post('/sync', 'processJsonData');
        }); ////////////////////////////////////////
    });

    // hr routes
    Route::group(['prefix' => 'requests'], function () {
        Route::controller(\Modules\HR\Http\Controllers\RequestController::class)->group(function () {
            Route::get('/employee-login', 'getEmployeeLogin');
            Route::get('/request-search', 'getRequestSearch');
            Route::post('/request-search-post', 'postRequestSearch');
            Route::post('/updateManager', 'updateManager')->name('requests.updateManager');
            Route::get('/', 'all')->name('requests.index');
            Route::get('/{id}', 'find')->name('requests.find');
            Route::post('/', 'create')->name('requests.create');
            Route::put('/{id}', 'update')->name('requests.update');
            Route::delete('/{id}', 'delete')->name('requests.destroy');
            Route::post("bulk-delete", "bulkDelete");
            Route::get('logs/{id}', 'logs')->name('requests.logs');

        });
    });

    //customTable
    Route::group(['prefix' => 'CustomTable'], function () {
        Route::controller(\Modules\HR\Http\Controllers\HRCustomTableController::class)->group(function () {
            Route::get('/seeder', 'seeder');

            Route::get('/', 'all')->name('customTable.index');
            Route::get('/table-columns/{tableName}', 'getCustomTableFields');
            Route::get('logs/{id}', 'logs')->name('customTable.logs');
            Route::get('/{id}', 'find');
            Route::post('/', 'create')->name('customTable.create');
            Route::put('/update', 'update')->name('customTable.update');
            Route::delete('/{id}', 'delete')->name('customTable.destroy');
            Route::post("bulk-delete", "bulkDelete");
        });
    });
    //fingerprint
    Route::group(['prefix' => 'fingerprint'], function () {
        Route::controller(\Modules\HR\Http\Controllers\FingerprintController::class)->group(function () {
            // Route::post('/show-data', 'showData')->name('fingerprint.showData');

            Route::get('/', 'all')->name('fingerprint.index');
            Route::post('/', 'create')->name('fingerprint.create');

        });
    });

    //time-tables-header

    Route::group(['prefix' => 'time-tables-header'], function () {
        Route::controller(\Modules\HR\Http\Controllers\TimeTablesHeaderController::class)->group(function () {
            Route::get('/get-drop-down', 'getDropDown')->name('time-tables-heade.getDropDown');

            Route::get('/', 'all')->name('time-tables-header.index');
            Route::get('/{id}', 'find')->name('time-tables-header.find');
            Route::post('/', 'create')->name('time-tables-header.create');
            Route::put('/{id}', 'update')->name('time-tables-header.update');
            Route::delete('/{id}', 'delete')->name('time-tables-header.destroy');
            Route::post("bulk-delete", "bulkDelete");
            Route::get('logs/{id}', 'logs')->name('time-tables-header.logs');
        });
    });
    //time-tables-detail

    Route::group(['prefix' => 'time-tables-detail'], function () {
        Route::controller(\Modules\HR\Http\Controllers\TimeTablesDetailController::class)->group(function () {
            Route::get('/', 'all')->name('time-tables-detail.index');
            Route::get('/{id}', 'find')->name('time-tables-detail.find');
            Route::post('/', 'create')->name('time-tables-detail.create');
            Route::put('/{id}', 'update')->name('time-tables-detail.update');
            Route::delete('/{id}', 'delete')->name('time-tables-detail.destroy');
            Route::post("bulk-delete", "bulkDelete");
            Route::get('logs/{id}', 'logs')->name('time-tables-detail.logs');
        });
    });

    Route::group(['prefix' => 'time-tables-types'], function () {
        Route::controller(\Modules\HR\Http\Controllers\TimeTableTypeController::class)->group(function () {
            Route::get('/', 'all')->name('time-tables-types.index');

        });
    });

    Route::group(['prefix' => 'attendance_settings'], function () {
        Route::controller(\Modules\HR\Http\Controllers\AttendanceSettingController::class)->group(function () {
            Route::get('/', 'all')->name('attendance_settings.index');
            Route::get('/{id}', 'find')->name('attendance_settings.find');
            Route::post('/', 'create')->name('attendance_settings.create');
            Route::put('/{id}', 'update')->name('attendance_settings.update');
            Route::delete('/{id}', 'delete')->name('attendance_settings.destroy');
            Route::post("bulk-delete", "bulkDelete");
            Route::get('logs/{id}', 'logs')->name('attendance_settings.logs');
        });
    });

    Route::group(['prefix' => 'employees-time-tables-header'], function () {
        Route::controller(\Modules\HR\Http\Controllers\EmployeesTimeTablesHeaderController::class)->group(function () {
            Route::get('/', 'all')->name('employees-time-tables-header.index');
            Route::get('/{id}', 'find')->name('employees-time-tables-header.find');
            Route::post('/', 'create')->name('employees-time-tables-header.create');
            Route::put('/{id}', 'update')->name('employees-time-tables-header.update');
            Route::delete('/{id}', 'delete')->name('employees-time-tables-header.destroy');
            Route::post("bulk-delete", "bulkDelete");
            Route::get('logs/{id}', 'logs')->name('employees-time-tables-header.logs');
        });
    });

    Route::group(['prefix' => 'employees-time-tables-detail'], function () {
        Route::controller(\Modules\HR\Http\Controllers\EmployeesTimeTablesDetailController::class)->group(function () {
/////////////////
            Route::get('/get-employees-time-tables-details', 'getEmployeesTimeTablesDetails')->name('employees-time-tables-detail.getEmployeesTimeTablesDetails');

            Route::get('/get-employees', 'getEmployees')->name('employees-time-tables-detail.getEmployees');
            Route::get('/', 'all')->name('employees-time-tables-detail.index');
            Route::get('/{id}', 'find')->name('employees-time-tables-detail.find');
            Route::post('/', 'create')->name('employees-time-tables-detail.create');
            Route::put('/{id}', 'update')->name('employees-time-tables-detail.update');
            Route::delete('/{id}', 'delete')->name('employees-time-tables-detail.destroy');
            Route::post("bulk-delete", "bulkDelete");
            Route::get('logs/{id}', 'logs')->name('employees-time-tables-detail.logs');
        });
    });

    //reports
    Route::group(['prefix' => 'reports'], function () {
        Route::controller(\Modules\HR\Http\Controllers\ReportController::class)->group(function () {

            Route::get('/attendance-departure', 'AttendanceDeparture')->name('reports.AttendanceDeparture');

        });
    });

    Route::group(['prefix' => 'statics'], function () {
        Route::controller(\Modules\HR\Http\Controllers\StaticesController::class)->group(function () {
            Route::get('/', 'all')->name('statics.index');

        });
    });

    Route::group(['prefix' => 'locations'], function () {
        Route::get('/', 'LocationController@all')->name('locations.all');
        Route::get('/logs/{id}', 'LocationController@logs')->name('locations.logs');
        Route::get('/{id}', 'LocationController@find')->name('locations.find');
        Route::post('/', 'LocationController@create')->name('locations.create');
        Route::put('/{id}', 'LocationController@update')->name('locations.update');
        Route::post("/bulk-delete", "LocationController@bulkDelete");
        Route::delete('/{id}', 'LocationController@delete')->name('locations.delete');
    });

    Route::group(['prefix' => 'location-dates'], function () {
        Route::get('/', 'LocationDateController@all')->name('location-dates.all');
        Route::get('/logs/{id}', 'LocationDateController@logs')->name('location-dates.logs');
        Route::get('/{id}', 'LocationDateController@find')->name('location-dates.find');
        Route::post('/', 'LocationDateController@create')->name('location-dates.create');
        Route::put('/{id}', 'LocationDateController@update')->name('location-dates.update');
        Route::post("/bulk-delete", "LocationDateController@bulkDelete");
        Route::delete('/{id}', 'LocationDateController@delete')->name('location-dates.delete');
    });

    Route::group(['prefix' => 'Employee-location-date'], function () {

        Route::get('/employees', 'EmployeeLocationDateController@employees')->name('Employee-location-date.employees');
        Route::get('/', 'EmployeeLocationDateController@all')->name('Employee-location-date.all');
        Route::get('/logs/{id}', 'EmployeeLocationDateController@logs')->name('Employee-location-date.logs');
        Route::get('/{id}', 'EmployeeLocationDateController@find')->name('Employee-location-date.find');
        Route::post('/', 'EmployeeLocationDateController@create')->name('Employee-location-date.create');
        Route::post('/update', 'EmployeeLocationDateController@update')->name('Employee-location-date.update');
        Route::post("/bulk-delete", "EmployeeLocationDateController@bulkDelete");
        Route::delete('/{id}', 'EmployeeLocationDateController@delete')->name('Employee-location-date.delete');

    });

    Route::group(['prefix' => 'public-holiday-header'], function () {
        Route::get('/', 'PublicHolidayHeaderController@all')->name('public-holiday-header.all');
        Route::get('/logs/{id}', 'PublicHolidayHeaderController@logs')->name('public-holiday-header.logs');
        Route::get('/{id}', 'PublicHolidayHeaderController@find')->name('public-holiday-header.find');
        Route::post('/', 'PublicHolidayHeaderController@create')->name('public-holiday-header.create');
        Route::put('/{id}', 'PublicHolidayHeaderController@update')->name('public-holiday-header.update');
        Route::post("/bulk-delete", "PublicHolidayHeaderController@bulkDelete");
        Route::delete('/{id}', 'PublicHolidayHeaderController@delete')->name('public-holiday-header.delete');
    });

    Route::group(['prefix' => 'employee-public-holiday-header'], function () {
        Route::get('/', 'EmployeePublicHolidayHeaderController@all')->name('employee-public-holiday-header.all');
        Route::get('/logs/{id}', 'EmployeePublicHolidayHeaderController@logs')->name('employee-public-holiday-header.logs');
        Route::get('/{id}', 'EmployeePublicHolidayHeaderController@find')->name('employee-public-holiday-header.find');
        Route::post('/', 'EmployeePublicHolidayHeaderController@create')->name('employee-public-holiday-header.create');
        Route::put('/{id}', 'EmployeePublicHolidayHeaderController@update')->name('employee-public-holiday-header.update');
        Route::post("/bulk-delete", "EmployeePublicHolidayHeaderController@bulkDelete");
        Route::delete('/{id}', 'EmployeePublicHolidayHeaderController@delete')->name('employee-public-holiday-header.delete');
    });


    Route::group(['prefix' => 'Employee-public-holiday-detail'], function () {
        Route::get('/', 'EmployeePublicHolidayDetailController@all')->name('EmployeePublicHolidayDetail.all');
        Route::get('/logs/{id}', 'EmployeePublicHolidayDetailController@logs')->name('EmployeePublicHolidayDetail.logs');
        Route::get('/{id}', 'EmployeePublicHolidayDetailController@find')->name('EmployeePublicHolidayDetail.find');
        Route::post('/', 'EmployeePublicHolidayDetailController@create')->name('EmployeePublicHolidayDetail.create');
        Route::post('/update', 'EmployeePublicHolidayDetailController@update')->name('EmployeePublicHolidayDetail.update');
        Route::post("/bulk-delete", "EmployeePublicHolidayDetailController@bulkDelete");
        Route::delete('/{id}', 'EmployeePublicHolidayDetailController@delete')->name('EmployeePublicHolidayDetail.delete');
    });

    Route::group(['prefix' => 'employee-fingerprint-headers'], function () {
        Route::get('/', 'EmployeeFingerprintHeaderController@all')->name('employee-fingerprint-headers.all');
        Route::get('/logs/{id}', 'EmployeeFingerprintHeaderController@logs')->name('employee-fingerprint-headers.logs');
        Route::get('/{id}', 'EmployeeFingerprintHeaderController@find')->name('employee-fingerprint-headers.find');
        Route::post('/', 'EmployeeFingerprintHeaderController@create')->name('employee-fingerprint-headers.create');
        Route::put('/{id}', 'EmployeeFingerprintHeaderController@update')->name('employee-fingerprint-headers.update');
        Route::post("/bulk-delete", "EmployeeFingerprintHeaderController@bulkDelete");
        Route::delete('/{id}', 'EmployeeFingerprintHeaderController@delete')->name('employee-fingerprint-headers.delete');
    });

    Route::group(['prefix' => 'employee-fingerprint-details'], function () {
        Route::get('/', 'EmployeeFingerprintDetailController@all')->name('employee-fingerprint-details.all');
        Route::get('/logs/{id}', 'EmployeeFingerprintDetailController@logs')->name('employee-fingerprint-details.logs');
        Route::get('/{id}', 'EmployeeFingerprintDetailController@find')->name('employee-fingerprint-details.find');
        Route::post('/', 'EmployeeFingerprintDetailController@create')->name('employee-fingerprint-details.create');
        Route::post('/update', 'EmployeeFingerprintDetailController@update')->name('employee-fingerprint-details.update');
        Route::post("/bulk-delete", "EmployeeFingerprintDetailController@bulkDelete");
        Route::delete('/{id}', 'EmployeeFingerprintDetailController@delete')->name('employee-fingerprint-details.delete');
    });



});
