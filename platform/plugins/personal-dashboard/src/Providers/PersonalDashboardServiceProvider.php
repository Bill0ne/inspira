<?php

namespace Botble\PersonalDashboard\Providers;

use Botble\Base\Facades\DashboardMenu;
use Botble\Base\Supports\DashboardMenuItem;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Support\ServiceProvider;

class PersonalDashboardServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register(): void
    {
        $this->app->register(HookServiceProvider::class);
    }

    public function boot(): void
    {
        $this
            ->setNamespace('plugins/personal-dashboard')
            ->loadAndPublishConfigurations(['general', 'permissions'])
            ->loadMigrations()
            ->loadRoutes()
            ->loadAndPublishViews()
            ->loadAndPublishTranslations();

        DashboardMenu::default()->beforeRetrieving(function (): void {
            DashboardMenu::make()
                ->registerItem(
                    DashboardMenuItem::make()
                        ->id('cms-plugins-personal-dashboard')
                        ->priority(350)
                        ->name('plugins/personal-dashboard::settings.menu_title')
                        ->icon('ti ti-layout-dashboard')
                )
                ->registerItem(
                    DashboardMenuItem::make()
                        ->id('cms-plugins-personal-dashboard-settings')
                        ->priority(0)
                        ->parentId('cms-plugins-personal-dashboard')
                        ->name('plugins/personal-dashboard::settings.menu_settings')
                        ->icon('ti ti-adjustments-cog')
                        ->route('personal-dashboard.settings.index')
                );
        });
    }
}
