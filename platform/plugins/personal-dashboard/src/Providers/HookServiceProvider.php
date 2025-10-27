<?php

namespace Botble\PersonalDashboard\Providers;

use Botble\Base\Facades\Assets;
use Botble\Base\Supports\ServiceProvider;
use Botble\Dashboard\Events\RenderingDashboardWidgets;
use Botble\Dashboard\Supports\DashboardWidgetInstance;
use Botble\PersonalDashboard\Models\PersonalDashboardCustomWidget;
use Botble\PersonalDashboard\Services\DashboardSettingsService;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

class HookServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app['events']->listen(RenderingDashboardWidgets::class, function (): void {
            add_action(DASHBOARD_ACTION_REGISTER_SCRIPTS, [$this, 'registerDashboardScripts'], 19);
            add_filter(DASHBOARD_FILTER_ADMIN_LIST, [$this, 'registerGreetingWidget'], 20, 2);
            add_filter(DASHBOARD_FILTER_ADMIN_LIST, [$this, 'registerCustomWidgets'], 21, 2);
            add_filter(DASHBOARD_FILTER_ADMIN_LIST, [$this, 'applyWidgetPreferences'], 200, 2);
        });
    }

    public function registerDashboardScripts(): void
    {
        Assets::addScriptsDirectly(['vendor/core/plugins/personal-dashboard/js/personal-dashboard.js']);
    }

    public function registerGreetingWidget(array $widgets, Collection $widgetSettings): array
    {
        $config = App::make(DashboardSettingsService::class)->getWidgetConfig('widget_personal_greeting');

        if (! Arr::get($config, 'enabled', true)) {
            return $widgets;
        }

        return (new DashboardWidgetInstance())
            ->setKey('widget_personal_greeting')
            ->setTitle(trans('plugins/personal-dashboard::widgets.greeting_title'))
            ->setIcon('ti ti-user-heart')
            ->setColor('primary')
            ->setPermission('dashboard.index')
            ->setRoute(route('personal-dashboard.widgets.greeting'))
            ->setColumn('col-12 col-md-6 col-xxl-4')
            ->setIsEqualHeight(false)
            ->setBodyClass('p-0 border-0 personal-dashboard-widget')
            ->init($widgets, $widgetSettings);
    }

    public function registerCustomWidgets(array $widgets, Collection $widgetSettings): array
    {
        if (! Schema::hasTable((new PersonalDashboardCustomWidget())->getTable())) {
            return $widgets;
        }

        $settingsService = App::make(DashboardSettingsService::class);

        $customWidgets = PersonalDashboardCustomWidget::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        if ($customWidgets->isEmpty()) {
            return $widgets;
        }

        $widgetInstance = new DashboardWidgetInstance();

        foreach ($customWidgets as $customWidget) {
            $config = $settingsService->getWidgetConfig($customWidget->key);

            if (! Arr::get($config, 'enabled', true)) {
                continue;
            }

            $routeName = $customWidget->ajax_route;

            if ($routeName && Route::has($routeName)) {
                $resolvedRoute = route($routeName);
            } else {
                $resolvedRoute = route('personal-dashboard.widgets.custom', $customWidget->key);
            }

            $widgetInstance
                ->setKey($customWidget->key)
                ->setTitle($customWidget->getTitle())
                ->setIcon($customWidget->icon ?: 'ti ti-layout-dashboard')
                ->setColor($customWidget->color ?: 'info')
                ->setPermission('dashboard.index')
                ->setRoute($resolvedRoute)
                ->setColumn($customWidget->column_class ?: 'col-12 col-md-6 col-xxl-4')
                ->setIsEqualHeight(false)
                ->setBodyClass('p-0 border-0 personal-dashboard-widget')
                ->setHasLoadCallback($customWidget->has_load_callback || (bool) $customWidget->ajax_route)
                ->setSettings($customWidget->settings ?? [])
                ->init($widgets, $widgetSettings);
        }

        return $widgets;
    }

    public function applyWidgetPreferences(array $widgets, Collection $widgetCollection): array
    {
        return App::make(DashboardSettingsService::class)->applyForUserWidgets($widgets, $widgetCollection);
    }
}
