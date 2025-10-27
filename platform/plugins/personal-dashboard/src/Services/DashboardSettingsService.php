<?php

namespace Botble\PersonalDashboard\Services;

use Botble\Dashboard\Models\DashboardWidget;
use Botble\Dashboard\Models\DashboardWidgetSetting;
use Botble\PersonalDashboard\Models\PersonalDashboardCustomWidget;
use Botble\PersonalDashboard\Models\PersonalDashboardSetting;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class DashboardSettingsService
{
    public const SETTINGS_CACHE_KEY = 'personal_dashboard.global_settings';

    protected array $defaults = [
        'allow_user_overrides' => true,
        'widgets' => [
            'widget_personal_greeting' => [
                'label' => 'Greeting',
                'enabled' => true,
                'order' => 10,
            ],
        ],
    ];

    public function __construct(protected CacheRepository $cache)
    {
    }

    public function getSettings(bool $refresh = false): array
    {
        if (! $refresh) {
            $cached = $this->cache->get(self::SETTINGS_CACHE_KEY);

            if (is_array($cached)) {
                return $cached;
            }
        }

        if (! $this->hasSettingsTable()) {
            $defaults = $this->defaults;

            [$normalizedWidgets] = $this->normalizeWidgets($defaults['widgets'] ?? []);

            $defaults['widgets'] = $normalizedWidgets;

            return $defaults;
        }

        $setting = PersonalDashboardSetting::query()->firstOrCreate([
            'key' => 'global',
        ]);

        $settings = array_merge($this->defaults, $setting->value ?? []);

        [$normalizedWidgets, $changed] = $this->normalizeWidgets($settings['widgets'] ?? []);

        $settings['widgets'] = $normalizedWidgets;

        if ($changed) {
            $setting->value = $settings;
            $setting->save();
        }

        $this->cache->forever(self::SETTINGS_CACHE_KEY, $settings);

        return $settings;
    }

    public function updateGeneral(array $data): void
    {
        $settings = $this->getSettings();

        $settings['allow_user_overrides'] = (bool) Arr::get($data, 'allow_user_overrides', false);

        $this->saveSettings($settings);
    }

    public function updateWidgets(array $widgets): void
    {
        $settings = $this->getSettings();

        foreach ($settings['widgets'] as $key => &$config) {
            $item = Arr::get($widgets, $key);

            if (! is_array($item)) {
                continue;
            }

            $config['enabled'] = (bool) Arr::get($item, 'enabled', false);
            $config['order'] = (int) Arr::get($item, 'order', $config['order'] ?? 0);
            $label = Arr::get($item, 'label');

            if ($label) {
                $config['label'] = $label;
            }
        }

        unset($config);

        $this->saveSettings($settings);
    }

    public function syncCustomWidget(PersonalDashboardCustomWidget $widget): void
    {
        if (! $this->hasSettingsTable()) {
            return;
        }

        $settings = $this->getSettings();

        $settings['widgets'][$widget->key] = array_merge($settings['widgets'][$widget->key] ?? [], [
            'label' => $widget->getTitle(),
            'enabled' => $widget->is_active,
            'order' => $widget->sort_order,
        ]);

        $this->saveSettings($settings);
    }

    public function removeCustomWidget(PersonalDashboardCustomWidget $widget): void
    {
        if (! $this->hasSettingsTable()) {
            return;
        }

        $settings = $this->getSettings();

        if (Arr::has($settings, "widgets.{$widget->key}")) {
            Arr::forget($settings, "widgets.{$widget->key}");
            $this->saveSettings($settings);
        }
    }

    public function getWidgetConfig(string $key): array
    {
        $settings = $this->getSettings();

        return Arr::get($settings, "widgets.{$key}", [
            'enabled' => true,
            'order' => 999,
        ]);
    }

    public function applyForUserWidgets(array $widgets, Collection $widgetCollection): array
    {
        $settings = $this->getSettings();
        $widgetsConfig = $settings['widgets'];
        $allowOverrides = (bool) Arr::get($settings, 'allow_user_overrides', true);

        $widgetCollectionById = $widgetCollection->keyBy('id');
        $widgetCollectionByName = $widgetCollection->keyBy('name');

        $filteredWidgets = array_values(array_filter($widgets, function (array $widget) use ($widgetsConfig, $widgetCollectionById) {
            if (Arr::get($widget, 'type') !== 'widget') {
                return true;
            }

            $model = $widgetCollectionById->get($widget['id'] ?? null);

            if (! $model) {
                return true;
            }

            $config = Arr::get($widgetsConfig, $model->name, ['enabled' => true]);

            return Arr::get($config, 'enabled', true);
        }));

        $widgetItems = array_values(array_filter($filteredWidgets, fn ($widget) => Arr::get($widget, 'type') === 'widget'));
        $statItems = array_values(array_filter($filteredWidgets, fn ($widget) => Arr::get($widget, 'type') !== 'widget'));

        $orders = [];
        foreach ($widgetItems as $widget) {
            $model = $widgetCollectionById->get($widget['id']);

            if (! $model) {
                continue;
            }

            $orders[$model->name] = Arr::get($widgetsConfig, "{$model->name}.order", 999);
        }

        usort($widgetItems, function (array $first, array $second) use ($orders, $widgetCollectionById) {
            $firstModel = $widgetCollectionById->get($first['id']);
            $secondModel = $widgetCollectionById->get($second['id']);

            $firstOrder = $firstModel ? Arr::get($orders, $firstModel->name, 999) : 999;
            $secondOrder = $secondModel ? Arr::get($orders, $secondModel->name, 999) : 999;

            return $firstOrder <=> $secondOrder;
        });

        if (! $allowOverrides) {
            $this->enforceUserSettings($widgetsConfig, $widgetCollectionByName);
        }

        return array_merge($widgetItems, $statItems);
    }

    protected function enforceUserSettings(array $widgetsConfig, $widgetCollectionByName): void
    {
        $userId = Auth::guard()->id();

        if (! $userId) {
            return;
        }

        $orderedWidgets = collect($widgetsConfig)
            ->filter(fn ($config) => Arr::get($config, 'enabled', true))
            ->sortBy('order');

        foreach ($orderedWidgets as $widgetName => $config) {
            $widget = $widgetCollectionByName->get($widgetName);

            if (! $widget) {
                continue;
            }

            DashboardWidgetSetting::query()->updateOrCreate(
                [
                    'widget_id' => $widget->getKey(),
                    'user_id' => $userId,
                ],
                [
                    'status' => 1,
                    'order' => (int) Arr::get($config, 'order', 0),
                ]
            );
        }

        $disabledWidgets = collect($widgetsConfig)
            ->filter(fn ($config) => ! Arr::get($config, 'enabled', true));

        foreach ($disabledWidgets as $widgetName => $config) {
            $widget = $widgetCollectionByName->get($widgetName);

            if (! $widget) {
                continue;
            }

            DashboardWidgetSetting::query()->updateOrCreate(
                [
                    'widget_id' => $widget->getKey(),
                    'user_id' => $userId,
                ],
                [
                    'status' => 0,
                    'order' => (int) Arr::get($config, 'order', 999),
                ]
            );
        }
    }

    protected function normalizeWidgets(array $widgets): array
    {
        $normalized = $widgets;
        $available = DashboardWidget::query()->pluck('name')->all();
        $changed = false;

        foreach ($available as $widgetKey) {
            if (! Arr::has($normalized, $widgetKey)) {
                $normalized[$widgetKey] = [
                    'label' => $this->makeLabel($widgetKey),
                    'enabled' => true,
                    'order' => count($normalized) + 1,
                ];
                $changed = true;
            }
        }

        $customWidgets = $this->hasCustomWidgetsTable()
            ? PersonalDashboardCustomWidget::query()->get()
            : collect();

        foreach ($customWidgets as $widget) {
            $existing = Arr::get($normalized, $widget->key, []);
            $updated = array_merge([
                'label' => $widget->getTitle(),
                'enabled' => $widget->is_active,
                'order' => $widget->sort_order,
            ], $existing);

            if ($existing != $updated) {
                $changed = true;
            }

            $normalized[$widget->key] = $updated;
        }

        uasort($normalized, fn ($a, $b) => ($a['order'] ?? 999) <=> ($b['order'] ?? 999));

        return [$normalized, $changed];
    }

    protected function saveSettings(array $settings): void
    {
        if (! $this->hasSettingsTable()) {
            return;
        }

        if (isset($settings['widgets']) && is_array($settings['widgets'])) {
            $settings['widgets'] = $this->sortWidgets($settings['widgets']);
        }

        PersonalDashboardSetting::query()->updateOrCreate([
            'key' => 'global',
        ], [
            'value' => $settings,
        ]);

        $this->cache->forget(self::SETTINGS_CACHE_KEY);
        $this->getSettings(true);
    }

    protected function sortWidgets(array $widgets): array
    {
        uasort($widgets, fn ($a, $b) => ($a['order'] ?? 999) <=> ($b['order'] ?? 999));

        return $widgets;
    }

    protected function makeLabel(string $key): string
    {
        $key = Str::after($key, 'widget_');

        return Str::of($key)->replace('_', ' ')->headline();
    }

    protected function hasSettingsTable(): bool
    {
        return Schema::hasTable((new PersonalDashboardSetting())->getTable());
    }

    protected function hasCustomWidgetsTable(): bool
    {
        return Schema::hasTable((new PersonalDashboardCustomWidget())->getTable());
    }
}
