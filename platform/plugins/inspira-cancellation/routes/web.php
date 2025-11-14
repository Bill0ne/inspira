<?php

use Botble\Base\Facades\AdminHelper;
use Botble\InspiraCancellation\Http\Controllers\Front\CancellationController as FrontCancellationController;
use Botble\InspiraCancellation\Http\Controllers\Front\TransferController as FrontTransferController;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Botble\\InspiraCancellation\\Http\\Controllers'], function (): void {

    /*
    |--------------------------------------------------------------------------
    | ADMIN ROUTES (Botble Backend)
    |--------------------------------------------------------------------------
    */

    AdminHelper::registerRoutes(function (): void {
        Route::group([
            'prefix' => 'inspira-cancellation',
            'as' => 'inspira-cancellation.',
        ], function (): void {

            /*
            |--------------------------------------------------------------------------
            | CANCELLATIONS – admin/inspira-cancellation/cancellations
            |--------------------------------------------------------------------------
            */
            Route::get('cancellations', [
                'as' => 'cancellations.index',
                'uses' => 'Admin\\CancellationController@index',
                'permission' => 'inspira-cancellation.cancellations.index',
            ]);

            Route::match(['GET', 'POST'], 'cancellations/list', [
                'as' => 'cancellations.list',
                'uses' => 'Admin\\CancellationController@list',
                'permission' => 'inspira-cancellation.cancellations.index',
            ]);

            Route::post('cancellations', [
                'as' => 'cancellations.data',
                'uses' => 'Admin\\CancellationController@getData',
                'permission' => 'inspira-cancellation.cancellations.index',
            ]);

            /*
            |--------------------------------------------------------------------------
            | TRANSFERS – admin/inspira-cancellation/transfers
            |--------------------------------------------------------------------------
            */
            Route::get('transfers', [
                'as' => 'transfers.index',
                'uses' => 'Admin\\TransferLogController@index',
                'permission' => 'inspira-cancellation.transfers.index',
            ]);

            Route::match(['GET', 'POST'], 'transfers/list', [
                'as' => 'transfers.list',
                'uses' => 'Admin\\TransferLogController@list',
                'permission' => 'inspira-cancellation.transfers.index',
            ]);

            Route::post('transfers', [
                'as' => 'transfers.data',
                'uses' => 'Admin\\TransferLogController@getData',
                'permission' => 'inspira-cancellation.transfers.index',
            ]);


            /*
            |--------------------------------------------------------------------------
            | RULES – admin/inspira-cancellation/rules
            |--------------------------------------------------------------------------
            */
            Route::group([
                'prefix' => 'rules',
                'as' => 'rules.',
            ], function (): void {

                // LIST
                Route::get('', [
                    'as' => 'index',
                    'uses' => 'Admin\\CancellationRuleController@index',
                    'permission' => 'inspira-cancellation.rules.index',
                ]);

                // DATATABLE JSON
                Route::post('', [
                    'as' => 'data',
                    'uses' => 'Admin\\CancellationRuleController@getData',
                    'permission' => 'inspira-cancellation.rules.index',
                ]);

                // CREATE
                Route::get('create', [
                    'as' => 'create',
                    'uses' => 'Admin\\CancellationRuleController@create',
                    'permission' => 'inspira-cancellation.rules.create',
                ]);

                Route::post('create', [
                    'as' => 'store',
                    'uses' => 'Admin\\CancellationRuleController@store',
                    'permission' => 'inspira-cancellation.rules.create',
                ]);

                // EDIT
                Route::get('{rule}/edit', [
                    'as' => 'edit',
                    'uses' => 'Admin\\CancellationRuleController@edit',
                    'permission' => 'inspira-cancellation.rules.edit',
                ]);

                Route::put('{rule}', [
                    'as' => 'update',
                    'uses' => 'Admin\\CancellationRuleController@update',
                    'permission' => 'inspira-cancellation.rules.edit',
                ]);

                // DELETE
                Route::delete('{rule}', [
                    'as' => 'destroy',
                    'uses' => 'Admin\\CancellationRuleController@destroy',
                    'permission' => 'inspira-cancellation.rules.destroy',
                ]);
            });
        });
    });

});


/*
|--------------------------------------------------------------------------
| CUSTOMER FRONTEND ROUTES (Profile)
|--------------------------------------------------------------------------
*/

Route::middleware(['web', 'customer'])
    ->prefix('profile/bookings')
    ->as('customer.bookings.')
    ->group(function (): void {

        // COURSE/HOTEL CANCELLATION
        Route::post('{type}/{booking}/cancel', [FrontCancellationController::class, 'store'])
            ->name('cancel');

        // TRANSFER SUBSTITUTE PARTICIPANT
        Route::post('{type}/{booking}/transfer', [FrontTransferController::class, 'store'])
            ->name('transfer');
    });

