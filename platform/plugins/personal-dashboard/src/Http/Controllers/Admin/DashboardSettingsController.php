<?php

namespace Botble\PersonalDashboard\Http\Controllers\Admin;

use Botble\Base\Http\Controllers\BaseController;
use Botble\PersonalDashboard\Models\PersonalDashboardCustomWidget;
use Botble\PersonalDashboard\Services\DashboardSettingsService;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DashboardSettingsController extends BaseController
{
    public function __construct(protected DashboardSettingsService $settingsService)
    {
    }

    public function index(): View|ViewFactory
    {
        $this->pageTitle(trans('plugins/personal-dashboard::settings.title'));

        $settings = $this->settingsService->getSettings();
        $customWidgets = PersonalDashboardCustomWidget::query()->orderBy('sort_order')->get();

        return view('plugins/personal-dashboard::settings.index', compact('settings', 'customWidgets'));
    }

    public function updateGeneral(Request $request): RedirectResponse
    {
        $this->settingsService->updateGeneral($request->all());

        return back()->with('success_msg', trans('core/base::notices.update_success_message'));
    }

    public function updateWidgets(Request $request): RedirectResponse
    {
        $widgets = $request->input('widgets', []);

        $this->settingsService->updateWidgets($widgets);

        return back()->with('success_msg', trans('core/base::notices.update_success_message'));
    }
}
