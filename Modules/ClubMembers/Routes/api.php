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

// Route::middleware('auth:api')->get('/clubmembers', function (Request $request) {
//     return $request->user();
// });

Route::prefix('club-members')->group(function () {
    // sponsers routes
    Route::group(['prefix' => 'sponsers'], function () {
        Route::get('/root-nodes', 'CmSponserController@getRootNodes')->name('modules.root-nodes');
        Route::get('/child-nodes/{parentId}', 'CmSponserController@getChildNodes')->name('modules.child-nodes');
        Route::get('/', 'CmSponserController@all')->name('cm-sponser.all');
        Route::get('/logs/{id}', 'CmSponserController@logs')->name('cm-sponser.logs');
        Route::get('/{id}', 'CmSponserController@find')->name('cm-sponser.find');
        Route::post('/', 'CmSponserController@create')->name('cm-sponser.create');
        Route::put('/{id}', 'CmSponserController@update')->name('cm-sponser.update');
        Route::post("/bulk-delete", "CmSponserController@bulkDelete");
        Route::delete('/{id}', 'CmSponserController@delete')->name('cm-sponser.delete');
    });

    // members routes
    Route::group(['prefix' => 'members'], function () {

        Route::get('/test-transfer', 'CmMemberController@TestTransfer');
        Route::get('/dataMemberTable', 'CmMemberController@dataMemberTable');
        Route::get('/dataMemberFildFullNameTable', 'CmMemberController@dataMemberFildFullNameTable');

        Route::get('/pending', 'CmMemberController@allAcceptancePending')->name('cm-members.allAcceptancePending');


        Route::get('/Acceptance', 'CmMemberController@allAcceptance')->name('cm-members.allAcceptance');

        Route::get('/', 'CmMemberController@all')->name('cm-members.all');
        Route::get('/logs/{id}', 'CmMemberController@logs')->name('cm-members.logs');
        Route::get('/{id}', 'CmMemberController@find')->name('cm-members.find');

        Route::post('/', 'CmMemberController@create')->name('cm-members.create');
        Route::put('/{id}', 'CmMemberController@update')->name('cm-members.update');
        Route::post("/bulk-delete", "CmMemberController@bulkDelete");
        Route::delete('/{id}', 'CmMemberController@delete')->name('cm-members.delete');

        Route::put('/accept-member/{id}', 'CmMemberController@acceptMember')->name('cm-members.acceptMember');
        Route::put('/decline-member/{id}', 'CmMemberController@declineMember')->name('cm-members.declineMember');

        Route::put('/sponsor/{sponsor_id}', 'CmMemberController@updateSponsor')->name('cm-members.updateSponsor');

        Route::post('/bulk-update', "CmMemberController@acceptMembers");
        Route::put('/update-accepted-members/{id}', 'CmMemberController@updateAcceptedMembers')->name('cm-members.updateAcceptedMembers');

    });

    // financial status routes
    Route::group(['prefix' => 'financial-status'], function () {

        Route::get('/', 'CmFinancialStatusController@all')->name('financial-status.all');
        Route::get('/logs/{id}', 'CmFinancialStatusController@logs')->name('cm-financial-status.logs');
        Route::get('/{id}', 'CmFinancialStatusController@find')->name('cm-financial-status.find');
        Route::post('/', 'CmFinancialStatusController@create')->name('cm-financial-status.create');
        Route::put('/{id}', 'CmFinancialStatusController@update')->name('cm-financial-status.update');
        Route::post("/bulk-delete", "CmFinancialStatusController@bulkDelete");
        Route::delete('/{id}', 'CmFinancialStatusController@delete')->name('cm-financial-status.delete');
    });

// members-types  routes
    Route::group(['prefix' => 'members-types'], function () {

        Route::get('/', 'CmMemberTypeController@all')->name('cm_members_types.all');
        Route::get('/logs/{id}', 'CmMemberTypeController@logs')->name('cm_members_types.logs');
        Route::get('/{id}', 'CmMemberTypeController@find')->name('cm_members_types.find');
        Route::post('/', 'CmMemberTypeController@create')->name('cm_members_types.create');
        Route::put('/{id}', 'CmMemberTypeController@update')->name('cm_members_types.update');
        Route::post("/bulk-delete", "CmMemberTypeController@bulkDelete");
        Route::delete('/{id}', 'CmMemberTypeController@delete')->name('cm_members_types.delete');
    });
// members-permissions  routes
    Route::group(['prefix' => 'members-permissions'], function () {

        Route::get('/', 'CmMemberPermissionController@all')->name('cm_members_permissions.all');
        Route::get('/logs/{id}', 'CmMemberPermissionController@logs')->name('cm_members_permissions.logs');
        Route::get('/{id}', 'CmMemberPermissionController@find')->name('cm_members_permissions.find');
        Route::post('/', 'CmMemberPermissionController@create')->name('cm_members_permissions.create');
        Route::put('/{id}', 'CmMemberPermissionController@update')->name('cm_members_permissions.update');
        Route::post("/bulk-delete", "CmMemberPermissionController@bulkDelete");
        Route::delete('/{id}', 'CmMemberPermissionController@delete')->name('cm_members_permissions.delete');
    });

    // members-types  routes
    Route::group(['prefix' => 'pending-members'], function () {

        Route::get('/', 'CmPendingMemberController@all')->name('cm_pending_members.all');
        Route::get('/logs/{id}', 'CmPendingMemberController@logs')->name('cm_pending_members.logs');
        Route::get('/{id}', 'CmPendingMemberController@find')->name('cm_pending_members.find');
        Route::post('/', 'CmPendingMemberController@create')->name('cm_pending_members.create');
        Route::put('/{id}', 'CmPendingMemberController@update')->name('cm_pending_members.update');
        Route::post("/bulk-delete", "CmPendingMemberController@bulkDelete");
        Route::delete('/{id}', 'CmPendingMemberController@delete')->name('cm_pending_members.delete');
    });

    // memberships-renewals  routes
    Route::group(['prefix' => 'memberships-renewals'], function () {

        Route::get('/', 'CmMembershipRenewalController@all')->name('cm_memberships_renewals.all');
        Route::get('/logs/{id}', 'CmMembershipRenewalController@logs')->name('cm_memberships_renewals.logs');
        Route::get('/{id}', 'CmMembershipRenewalController@find')->name('cm_memberships_renewals.find');
        Route::post('/', 'CmMembershipRenewalController@create')->name('cm_memberships_renewals.create');
        Route::put('/{id}', 'CmMembershipRenewalController@update')->name('cm_memberships_renewals.update');
        Route::post("/bulk-delete", "CmMembershipRenewalController@bulkDelete");
        Route::delete('/{id}', 'CmMembershipRenewalController@delete')->name('cm_memberships_renewals.delete');
    });

    // members-settings  routes
    Route::group(['prefix' => 'members-setting'], function () {

        Route::get('/', 'CmMemberSettingController@all')->name('cm-member-setting.all');
        Route::get('/logs/{id}', 'CmMemberSettingController@logs')->name('cm-member-setting.logs');
        Route::get('/{id}', 'CmMemberSettingController@find')->name('cm-member-setting.find');
        Route::post('/', 'CmMemberSettingController@create')->name('cm-member-setting.create');
        Route::put('/{id}', 'CmMemberSettingController@update')->name('cm-member-setting.update');
        Route::post("/bulk-delete", "CmMemberSettingController@bulkDelete");
        Route::delete('/{id}', 'CmMemberSettingController@delete')->name('cm-member-setting.delete');
    });

    Route::group(['prefix' => 'type-permission'], function () {
        Route::get('/', 'CmTypePermissionController@all')->name('type-permission.all');
        Route::get('/logs/{id}', 'CmTypePermissionController@logs')->name('type-permission.logs');
        Route::get('/{id}', 'CmTypePermissionController@find')->name('type-permission.find');
        Route::post('/', 'CmTypePermissionController@create')->name('type-permission.create');
        Route::put('/{id}', 'CmTypePermissionController@update')->name('type-permission.update');
        Route::post("/bulk-delete", "CmTypePermissionController@bulkDelete");
        Route::delete('/{id}', 'CmTypePermissionController@delete')->name('type-permission.delete');
    });

    Route::group(['prefix' => 'transactions'], function () {
        Route::get('/', 'CmTransactionController@all')->name('transaction.all');
        Route::get('/logs/{id}', 'CmTransactionController@logs')->name('transaction.logs');
        Route::get('/{id}', 'CmTransactionController@find')->name('transaction.find');
        Route::post('/', 'CmTransactionController@create')->name('transaction.create');
        Route::put('/{id}', 'CmTransactionController@update')->name('transactionupdate');
        Route::post("/bulk-delete", "CmTransactionController@bulkDelete");
        Route::delete('/{id}', 'CmTransactionController@delete')->name('transaction.delete');
    });

});
