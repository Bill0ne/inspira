<?php

use Botble\Base\Facades\AdminHelper;
use Botble\Community\Http\Controllers\CommunityMemberController;
use Botble\Community\Http\Controllers\PublicController;
use Botble\Theme\Facades\Theme;
use Illuminate\Support\Facades\Route;

AdminHelper::registerRoutes(function (): void {
    Route::group(['prefix' => 'community', 'as' => 'community.'], function (): void {
        Route::resource('', CommunityMemberController::class)->parameters(['' => 'community']);
    });
});

Route::group(['namespace' => 'Botble\Community\Http\Controllers', 'middleware' => ['web', 'core']], function (): void {
    if (defined('THEME_MODULE_SCREEN_NAME')) {
        Theme::registerRoutes(function (): void {
            Route::get('community', [PublicController::class, 'index'])->name('public.community');
        });
    }
});
