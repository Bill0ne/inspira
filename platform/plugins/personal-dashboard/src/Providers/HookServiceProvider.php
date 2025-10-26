<?php

namespace Botble\PersonalDashboard\Providers;

use Botble\Base\Supports\ServiceProvider;
use Botble\Dashboard\Events\RenderingDashboardWidgets;
use Botble\Dashboard\Supports\DashboardWidgetInstance;
use Illuminate\Support\Collection;

class HookServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app['events']->listen(RenderingDashboardWidgets::class, function (): void {
            add_filter(DASHBOARD_FILTER_ADMIN_LIST, [$this, 'registerGreetingWidget'], 20, 2);
        });
    }

    public function registerGreetingWidget(array $widgets, Collection $widgetSettings): array
    {
        return (new DashboardWidgetInstance())
            ->setKey('widget_personal_greeting')
            ->setTitle(trans('plugins/personal-dashboard::widgets.greeting_title'))
            ->setIcon('ti ti-user-heart')
            ->setColor('primary')
            ->setPermission('dashboard.index')
            ->setRoute(route('personal-dashboard.widgets.greeting'))
            ->setColumn('col-12 col-md-6 col-xxl-4')
            ->setIsEqualHeight(false)
            ->setBodyClass('p-0 border-0')
            ->init($widgets, $widgetSettings);
    }
}
