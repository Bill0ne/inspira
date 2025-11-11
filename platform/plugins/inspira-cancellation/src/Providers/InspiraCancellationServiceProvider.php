<?php

namespace Botble\InspiraCancellation\Providers;

use Botble\Base\Facades\DashboardMenu;
use Botble\Base\Supports\DashboardMenuItem;
use Botble\Base\Supports\ServiceProvider;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\Base\Facades\EmailHandler;
use Botble\InspiraCancellation\Services\CancellationService;
use Botble\InspiraCancellation\Services\TransferService;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Routing\Events\RouteMatched;

class InspiraCancellationServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register(): void
    {
        $this->app->singleton(CancellationService::class, function (): CancellationService {
            return new CancellationService();
        });

        $this->app->singleton('inspira.cancellation', function () {
            return $this->app->make(CancellationService::class);
        });

        $this->app->singleton(TransferService::class, function (): TransferService {
            return new TransferService();
        });

        $this->app->singleton('inspira.cancellation.transfer', function () {
            return $this->app->make(TransferService::class);
        });

        AliasLoader::getInstance()->alias(
            'InspiraCancellation',
            \Botble\InspiraCancellation\Facades\InspiraCancellation::class
        );
    }

    public function boot(): void
    {
        $this->app->register(EventServiceProvider::class);

        $this
            ->setNamespace('plugins/inspira-cancellation')
            ->loadHelpers()
            ->loadAndPublishConfigurations(['permissions', 'email'])
            ->loadAndPublishTranslations()
            ->loadAndPublishViews()
            ->loadRoutes()
            ->loadMigrations();

        $this->app['events']->listen(RouteMatched::class, function (): void {
            DashboardMenu::default()->beforeRetrieving(function (): void {
                $parentId = 'cms-plugins-hotel';

                if (function_exists('is_plugin_active') && is_plugin_active('admin-sidebar')) {
                    $parentId = 'cms-custom-inspira-management';
                }

                DashboardMenu::make()
                    ->registerItem(
                        DashboardMenuItem::make()
                            ->id('cms-plugins-inspira-cancellation')
                            ->priority(45)
                            ->parentId($parentId)
                            ->name('plugins/inspira-cancellation::cancellation.menu')
                            ->icon('ti ti-calendar-cancel')
                    )
                    ->registerItem(
                        DashboardMenuItem::make()
                            ->id('cms-plugins-inspira-cancellation-cancellations')
                            ->priority(1)
                            ->parentId('cms-plugins-inspira-cancellation')
                            ->name('plugins/inspira-cancellation::cancellation.cancellation.list')
                            ->icon('ti ti-file-invoice')
                            ->route('inspira-cancellation.cancellations.index')
                    )
                    ->registerItem(
                        DashboardMenuItem::make()
                            ->id('cms-plugins-inspira-cancellation-rules')
                            ->priority(2)
                            ->parentId('cms-plugins-inspira-cancellation')
                            ->name('plugins/inspira-cancellation::cancellation.rule.list')
                            ->icon('ti ti-adjustments-cog')
                            ->route('inspira-cancellation.rules.index')
                    )
                    ->registerItem(
                        DashboardMenuItem::make()
                            ->id('cms-plugins-inspira-cancellation-transfers')
                            ->priority(3)
                            ->parentId('cms-plugins-inspira-cancellation')
                            ->name('plugins/inspira-cancellation::cancellation.transfer.list')
                            ->icon('ti ti-arrows-left-right')
                            ->route('inspira-cancellation.transfers.index')
                    );
            });

            EmailHandler::addTemplateSettings(
                'inspira-cancellation',
                config('plugins.inspira-cancellation.email')
            );
        });
    }
}
