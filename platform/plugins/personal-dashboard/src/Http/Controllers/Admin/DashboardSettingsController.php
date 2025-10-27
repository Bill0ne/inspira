<?php

namespace Botble\PersonalDashboard\Http\Controllers\Admin;

use Botble\Base\Http\Controllers\BaseController;
use Botble\PersonalDashboard\Services\DashboardSettingsService;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Botble\PersonalDashboard\Models\PersonalDashboardCustomWidget;

class DashboardSettingsController extends BaseController
{
    public function __construct(protected DashboardSettingsService $settingsService)
    {
    }

    public function index(): View|ViewFactory
    {
        $this->pageTitle(trans('plugins/personal-dashboard::settings.title'));

        $tablesReady = $this->settingsService->tablesReady();
        $customWidgetsReady = $this->settingsService->customWidgetsReady();

        $settings = $this->settingsService->getSettings();
        $customWidgets = $customWidgetsReady
            ? PersonalDashboardCustomWidget::query()->orderBy('sort_order')->get()
            : collect();

        return view(
            'plugins/personal-dashboard::settings.index',
            compact('settings', 'customWidgets', 'tablesReady', 'customWidgetsReady')
        );
    }

    public function updateGeneral(Request $request): RedirectResponse
    {
        if (! $this->settingsService->updateGeneral($request->all())) {
            return back()->with(
                'error_msg',
                trans('plugins/personal-dashboard::settings.alerts.migrations_pending', [
                    'command' => 'php artisan migrate --path=platform/plugins/personal-dashboard/database/migrations',
                ])
            );
        }

        return back()->with('success_msg', trans('core/base::notices.update_success_message'));
    }

    public function updateWidgets(Request $request): RedirectResponse
    {
        $widgets = $request->input('widgets', []);

        if (! $this->settingsService->updateWidgets($widgets)) {
            return back()->with(
                'error_msg',
                trans('plugins/personal-dashboard::settings.alerts.migrations_pending', [
                    'command' => 'php artisan migrate --path=platform/plugins/personal-dashboard/database/migrations',
                ])
            );
        }

        return back()->with('success_msg', trans('core/base::notices.update_success_message'));
    }
}
