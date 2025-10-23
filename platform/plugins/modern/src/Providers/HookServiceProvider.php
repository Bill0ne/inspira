<?php

namespace Botble\Modern\Providers;

use Botble\Base\Supports\ServiceProvider;
use Botble\Dashboard\Events\RenderingDashboardWidgets;
use Botble\Dashboard\Supports\DashboardWidgetInstance;
use Illuminate\Support\Collection;

class HookServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app['events']->listen(RenderingDashboardWidgets::class, function (): void {
            add_filter(DASHBOARD_FILTER_ADMIN_LIST, [$this, 'registerDashboardWidgets'], 45, 2);
        });
    }

    public function registerDashboardWidgets(array $widgets, Collection $widgetSettings): array
    {
        $dashboardWidget = new DashboardWidgetInstance();

        return $dashboardWidget
            ->setPermission('dashboard.index')
            ->setKey('widget_modern_greeting')
            ->setTitle(trans('plugins/modern::widgets.greeting_title'))
            ->setIcon('ti ti-hand-stop')
            ->setColor('primary')
            ->setRoute(route('modern.widgets.greeting'))
            ->setBodyClass('modern-greeting-widget')
            ->setColumn('col-12')
            ->init($widgets, $widgetSettings);
    }
}
