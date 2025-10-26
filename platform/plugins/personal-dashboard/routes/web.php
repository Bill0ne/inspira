<?php

use Botble\Base\Facades\AdminHelper;
use Botble\PersonalDashboard\Http\Controllers\GreetingWidgetController;
use Illuminate\Support\Facades\Route;

AdminHelper::registerRoutes(function (): void {
    Route::group([
        'prefix' => 'personal-dashboard',
        'as' => 'personal-dashboard.',
        'permission' => 'dashboard.index',
    ], function (): void {
        Route::get('widgets/greeting', GreetingWidgetController::class)
            ->name('widgets.greeting');
    });
});
