<?php

use Illuminate\Support\Facades\Route;
use Botble\Base\Facades\AdminHelper;
use Botble\PriceConfigurator\Http\Controllers\CustomerCategoryController;
use Botble\PriceConfigurator\Http\Controllers\RuleController;
use Botble\PriceConfigurator\Http\Controllers\TierController;
use Botble\PriceConfigurator\Http\Controllers\QuantityDiscountController;

AdminHelper::registerRoutes(function () {

    Route::group(['prefix' => 'customer-categories', 'as' => 'customer-category.'], function () {
        Route::resource('', CustomerCategoryController::class)->parameters(['' => 'customer_category']);
    });

    Route::group(['prefix' => 'rules', 'as' => 'rule.'], function () {
        Route::resource('', RuleController::class)->parameters(['' => 'rule']);

        Route::get('list', [RuleController::class, 'ajaxTargets'])->name('list');

    });

    Route::group(['prefix' => 'tiers', 'as' => 'tier.'], function () {
        Route::resource('', TierController::class)->parameters(['' => 'tier']);
    });

    Route::group(['prefix' => 'quantity-discounts', 'as' => 'quantity-discount.'], function () {
        Route::resource('', QuantityDiscountController::class)->parameters(['' => 'quantity_discount']);
    });

});
