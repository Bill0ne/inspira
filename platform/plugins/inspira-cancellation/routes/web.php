<?php

use Botble\Base\Facades\AdminHelper;
use Botble\InspiraCancellation\Http\Controllers\Front\CancellationController as FrontCancellationController;
use Botble\InspiraCancellation\Http\Controllers\Front\TransferController as FrontTransferController;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Botble\\InspiraCancellation\\Http\\Controllers'], function (): void {
    AdminHelper::registerRoutes(function (): void {
        Route::group([
            'prefix' => 'inspira-cancellation',
            'as' => 'inspira-cancellation.',
        ], function (): void {
            Route::get('cancellations', [
                'as' => 'cancellations.index',
                'uses' => 'Admin\\CancellationController@index',
                'permission' => 'inspira-cancellation.cancellations.index',
            ]);

            Route::get('transfers', [
                'as' => 'transfers.index',
                'uses' => 'Admin\\TransferLogController@index',
                'permission' => 'inspira-cancellation.transfers.index',
            ]);

            Route::group(['prefix' => 'rules', 'as' => 'rules.'], function (): void {
                Route::get('', [
                    'as' => 'index',
                    'uses' => 'Admin\\CancellationRuleController@index',
                    'permission' => 'inspira-cancellation.rules.index',
                ]);

                Route::get('create', [
                    'as' => 'create',
                    'uses' => 'Admin\\CancellationRuleController@create',
                    'permission' => 'inspira-cancellation.rules.create',
                ]);

                Route::post('', [
                    'as' => 'store',
                    'uses' => 'Admin\\CancellationRuleController@store',
                    'permission' => 'inspira-cancellation.rules.create',
                ]);

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

                Route::delete('{rule}', [
                    'as' => 'destroy',
                    'uses' => 'Admin\\CancellationRuleController@destroy',
                    'permission' => 'inspira-cancellation.rules.destroy',
                ]);
            });
        });
    });
});

Route::middleware(['web', 'customer'])
    ->prefix('profile/bookings')
    ->as('customer.bookings.')
    ->group(function (): void {
        Route::post('{type}/{booking}/cancel', [FrontCancellationController::class, 'store'])
            ->name('cancel');

        Route::post('{type}/{booking}/transfer', [FrontTransferController::class, 'store'])
            ->name('transfer');
    });
