<?php

use Botble\Base\Facades\AdminHelper;
use Botble\InspiraCancellation\Http\Controllers\Admin\CancellationController;
use Botble\InspiraCancellation\Http\Controllers\Admin\CancellationRuleController;
use Botble\InspiraCancellation\Http\Controllers\Admin\TransferLogController;
use Botble\InspiraCancellation\Http\Controllers\Front\CancellationController as FrontCancellationController;
use Botble\InspiraCancellation\Http\Controllers\Front\TransferController as FrontTransferController;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Botble\\InspiraCancellation\\Http\\Controllers'], function (): void {
    AdminHelper::registerRoutes(function (): void {
        Route::group([
            'prefix' => 'inspira-cancellation',
            'as' => 'inspira-cancellation.',
        ], function (): void {
            Route::get('cancellations', [CancellationController::class, 'index'])
                ->name('cancellations.index')
                ->middleware('permission:inspira-cancellation.cancellations.index');

            Route::get('transfers', [TransferLogController::class, 'index'])
                ->name('transfers.index')
                ->middleware('permission:inspira-cancellation.transfers.index');

            Route::group(['prefix' => 'rules', 'as' => 'rules.'], function (): void {
                Route::get('', [CancellationRuleController::class, 'index'])
                    ->name('index')
                    ->middleware('permission:inspira-cancellation.rules.index');

                Route::get('create', [CancellationRuleController::class, 'create'])
                    ->name('create')
                    ->middleware('permission:inspira-cancellation.rules.create');

                Route::post('', [CancellationRuleController::class, 'store'])
                    ->name('store')
                    ->middleware('permission:inspira-cancellation.rules.create');

                Route::get('{rule}/edit', [CancellationRuleController::class, 'edit'])
                    ->name('edit')
                    ->middleware('permission:inspira-cancellation.rules.edit');

                Route::put('{rule}', [CancellationRuleController::class, 'update'])
                    ->name('update')
                    ->middleware('permission:inspira-cancellation.rules.edit');

                Route::delete('{rule}', [CancellationRuleController::class, 'destroy'])
                    ->name('destroy')
                    ->middleware('permission:inspira-cancellation.rules.destroy');
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
