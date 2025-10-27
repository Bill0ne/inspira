<?php

namespace Botble\PersonalDashboard\Http\Controllers\Admin;

use Botble\Base\Http\Controllers\BaseController;
use Botble\PersonalDashboard\Models\PersonalDashboardCustomWidget;
use Botble\PersonalDashboard\Services\DashboardSettingsService;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CustomWidgetController extends BaseController
{
    public function __construct(protected DashboardSettingsService $settingsService)
    {
    }

    public function create(): View|ViewFactory
    {
        $this->pageTitle(trans('plugins/personal-dashboard::settings.custom_widgets.create_title'));

        $locales = $this->getLocales();

        $widget = new PersonalDashboardCustomWidget([
            'settings' => [
                'show_predefined_ranges' => false,
            ],
            'column_class' => 'col-12 col-md-6 col-xxl-4',
            'view_path' => 'plugins/personal-dashboard::widgets.custom-card',
            'title' => array_fill_keys(array_keys($locales), ''),
            'description' => array_fill_keys(array_keys($locales), ''),
        ]);

        return view('plugins/personal-dashboard::settings.custom-widgets.form', [
            'widget' => $widget,
            'locales' => $locales,
            'submitRoute' => route('personal-dashboard.settings.custom-widgets.store'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateRequest($request);

        $widget = PersonalDashboardCustomWidget::query()->create($data);

        $this->settingsService->syncCustomWidget($widget);

        return redirect()
            ->route('personal-dashboard.settings.index')
            ->with('success_msg', trans('core/base::notices.create_success_message'));
    }

    public function edit(PersonalDashboardCustomWidget $customWidget): View|ViewFactory
    {
        $this->pageTitle(trans('plugins/personal-dashboard::settings.custom_widgets.edit_title'));

        return view('plugins/personal-dashboard::settings.custom-widgets.form', [
            'widget' => $customWidget,
            'locales' => $this->getLocales(),
            'submitRoute' => route('personal-dashboard.settings.custom-widgets.update', $customWidget),
        ]);
    }

    public function update(PersonalDashboardCustomWidget $customWidget, Request $request): RedirectResponse
    {
        $data = $this->validateRequest($request, $customWidget);

        $customWidget->fill($data);
        $customWidget->save();

        $this->settingsService->syncCustomWidget($customWidget);

        return redirect()
            ->route('personal-dashboard.settings.index')
            ->with('success_msg', trans('core/base::notices.update_success_message'));
    }

    public function destroy(PersonalDashboardCustomWidget $customWidget): RedirectResponse
    {
        $customWidget->delete();

        $this->settingsService->removeCustomWidget($customWidget);

        return redirect()
            ->route('personal-dashboard.settings.index')
            ->with('success_msg', trans('core/base::notices.delete_success_message'));
    }

    protected function validateRequest(Request $request, ?PersonalDashboardCustomWidget $widget = null): array
    {
        $locales = $this->getLocales();

        $rules = [
            'key' => ['required', 'string', 'max:191', 'regex:/^[a-z0-9_\-\.]+$/'],
            'icon' => ['nullable', 'string', 'max:191'],
            'color' => ['nullable', 'string', 'max:191'],
            'column_class' => ['nullable', 'string', 'max:191'],
            'view_path' => ['nullable', 'string', 'max:191'],
            'handler' => ['nullable', 'string', 'max:191'],
            'ajax_route' => ['nullable', 'string', 'max:191'],
            'has_load_callback' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];

        foreach (array_keys($locales) as $locale) {
            $rules["title.{$locale}"] = ['required', 'string', 'max:255'];
            $rules["description.{$locale}"] = ['nullable', 'string'];
        }

        if ($widget) {
            $rules['key'][] = Rule::unique('personal_dashboard_custom_widgets', 'key')->ignore($widget->getKey());
        } else {
            $rules['key'][] = Rule::unique('personal_dashboard_custom_widgets', 'key');
        }

        $data = $request->validate($rules);

        $data['has_load_callback'] = (bool) Arr::get($data, 'has_load_callback', false);
        $data['is_active'] = (bool) Arr::get($data, 'is_active', false);

        if (Arr::has($data, 'sort_order') && Arr::get($data, 'sort_order') !== null) {
            $data['sort_order'] = (int) Arr::get($data, 'sort_order', 0);
        } elseif ($widget) {
            $data['sort_order'] = $widget->sort_order;
        } else {
            $data['sort_order'] = (int) (PersonalDashboardCustomWidget::query()->max('sort_order') + 1);
        }

        $settingsJson = trim((string) $request->input('settings_json'));

        if ($settingsJson !== '') {
            $decoded = json_decode($settingsJson, true);

            if (json_last_error() !== JSON_ERROR_NONE || ! is_array($decoded)) {
                throw ValidationException::withMessages([
                    'settings_json' => trans('plugins/personal-dashboard::settings.custom_widgets.invalid_settings_json'),
                ]);
            }

            $data['settings'] = $decoded;
        } else {
            $data['settings'] = $widget ? (array) $widget->settings : [];
        }

        unset($data['settings_json']);

        return $data;
    }

    protected function getLocales(): array
    {
        if (class_exists(\Botble\Language\Facades\Language::class)) {
            return \Botble\Language\Facades\Language::getLocales();
        }

        $locale = app()->getLocale();

        return [$locale => $locale];
    }
}
