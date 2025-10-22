<?php

namespace Botble\PriceConfigurator\Providers;

use Botble\Base\Facades\DashboardMenu;
use Botble\Base\Supports\DashboardMenuItem;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Support\ServiceProvider;

class PriceConfiguratorServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register(): void
    {

    }

    public function boot(): void
    {
        $this
            ->setNamespace('plugins/price-configurator')
            ->loadHelpers()
            ->loadAndPublishTranslations()
            ->loadRoutes()
            ->loadAndPublishViews()
            ->loadMigrations()
            ->publishAssets();

        DashboardMenu::default()->beforeRetrieving(function (): void {
            DashboardMenu::make()
                ->registerItem(
                    DashboardMenuItem::make()
                        ->id('cms-plugins-price-configurator')
                        ->priority(4)
                        ->name('plugins/price-configurator::price-configurator.name')
                        ->icon('ti ti-calculator')
                )
                ->registerItem(
                    DashboardMenuItem::make()
                        ->id('cms-plugins-price-configurator-customer-category')
                        ->priority(3)
                        ->parentId('cms-plugins-price-configurator')
                        ->name('plugins/price-configurator::price-configurator.customer-category.name')
                        ->icon('ti ti-users-group')
                        ->route('customer-category.index')
                )
                ->registerItem(
                    DashboardMenuItem::make()
                        ->id('cms-plugins-price-configurator-tiers')
                        ->priority(2)
                        ->parentId('cms-plugins-price-configurator')
                        ->name('plugins/price-configurator::price-configurator.tier.name')
                        ->icon('ti ti-stairs-up')
                        ->route('tier.index')
                )
                ->registerItem(
                    DashboardMenuItem::make()
                        ->id('cms-plugins-price-configurator-rules')
                        ->priority(1)
                        ->parentId('cms-plugins-price-configurator')
                        ->name('plugins/price-configurator::price-configurator.rule.name')
                        ->icon('ti ti-adjustments-cog')
                        ->route('rule.index')
                )
                ->registerItem(
                    DashboardMenuItem::make()
                        ->id('cms-plugins-price-configurator-quantity-discount')
                        ->priority(4)
                        ->parentId('cms-plugins-price-configurator')
                        ->name('plugins/price-configurator::price-configurator.quantity-discount.name')
                        ->icon('ti ti-discount-2')
                        ->route('quantity-discount.index')
                );
        });

    }
}
