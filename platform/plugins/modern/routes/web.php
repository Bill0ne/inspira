<?php

use Botble\Base\Facades\AdminHelper;
use Illuminate\Support\Facades\Route;

AdminHelper::registerRoutes(function (): void {
    Route::group([
        'namespace' => 'Botble\\Modern\\Http\\Controllers',
        'prefix' => 'modern',
        'as' => 'modern.',
    ], function (): void {
        Route::get('widgets/greeting', [
            'as' => 'widgets.greeting',
            'uses' => 'ModernController@greeting',
            'permission' => 'dashboard.index',
        ]);
    });
});
