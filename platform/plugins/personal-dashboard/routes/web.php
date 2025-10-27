<?php

use Botble\Base\Facades\AdminHelper;
use Botble\PersonalDashboard\Http\Controllers\Admin\CustomWidgetController;
use Botble\PersonalDashboard\Http\Controllers\Admin\DashboardSettingsController;
use Botble\PersonalDashboard\Http\Controllers\CustomWidgetDisplayController;
use Botble\PersonalDashboard\Http\Controllers\GreetingWidgetController;
use Illuminate\Support\Facades\Route;

AdminHelper::registerRoutes(function (): void {
    Route::group([
        'prefix' => 'personal-dashboard',
        'as' => 'personal-dashboard.',
    ], function (): void {
        Route::group([
            'permission' => 'dashboard.index',
        ], function (): void {
            Route::get('widgets/greeting', GreetingWidgetController::class)
                ->name('widgets.greeting');

            Route::get('widgets/custom/{widget}', [CustomWidgetDisplayController::class, 'show'])
                ->name('widgets.custom');
        });

        Route::group([
            'prefix' => 'settings',
            'as' => 'settings.',
            'permission' => 'personal-dashboard.settings.index',
        ], function (): void {
            Route::get('/', [DashboardSettingsController::class, 'index'])->name('index');
            Route::post('general', [DashboardSettingsController::class, 'updateGeneral'])->name('general');
            Route::post('widgets', [DashboardSettingsController::class, 'updateWidgets'])->name('widgets');

            Route::group([
                'permission' => 'personal-dashboard.settings.custom-widgets',
            ], function (): void {
                Route::resource('custom-widgets', CustomWidgetController::class)
                    ->except(['index', 'show'])
                    ->parameters(['custom-widgets' => 'customWidget'])
                    ->names('custom-widgets');
            });
        });
    });
});
