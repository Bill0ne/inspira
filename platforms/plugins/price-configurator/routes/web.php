<?php

use Illuminate\Support\Facades\Route;
use Botble\Base\Facades\BaseHelper;

use Botble\PriceConfigurator\Http\Controllers\AdminController;
use Botble\PriceConfigurator\Http\Controllers\CategoryController;
use Botble\PriceConfigurator\Http\Controllers\RuleController;
use Botble\PriceConfigurator\Http\Controllers\TierController;
use Botble\PriceConfigurator\Http\Controllers\DiscountController;

Route::group([
    'prefix' => BaseHelper::getAdminPrefix(),
    'middleware' => ['web', 'core'],
], function () {

    // Original: Dashboard
    Route::get('price-configurator', [AdminController::class, 'index'])->name('pc.admin.index');

    // Original: Kundenkategorien
    Route::resource('price-configurator/categories', CategoryController::class)->names('pc.categories');
    Route::post('price-configurator/categories/{id}/toggle', [CategoryController::class, 'toggle'])->name('pc.categories.toggle');

    // Original: Preisstufen (Tiers)
    Route::resource('price-configurator/tiers', TierController::class)->names('pc.tiers');
    Route::post('price-configurator/tiers/{id}/toggle', [TierController::class, 'toggle'])->name('pc.tiers.toggle');

    // Original: Regeln
    Route::resource('price-configurator/rules', RuleController::class)->names('pc.rules');
    Route::post('price-configurator/rules/{id}/toggle', [RuleController::class, 'toggle'])->name('pc.rules.toggle');

    // Neu: Mengen- & Spezialrabatte (Staffelung)
    Route::resource('price-configurator/discounts', DiscountController::class)->names('pc.discounts');
    Route::post('price-configurator/discounts/{id}/toggle', [DiscountController::class, 'toggle'])->name('pc.discounts.toggle');
});
