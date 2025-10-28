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

    public function updateGeneral(array $data): bool
    {
        if (! $this->hasSettingsTable()) {
            return false;
        }

        $settings = $this->getSettings();

        $settings['allow_user_overrides'] = (bool) Arr::get($data, 'allow_user_overrides', false);

        return $this->saveSettings($settings);
    }

    public function updateWidgets(array $widgets): bool
    {
        if (! $this->hasSettingsTable()) {
            return false;
        }

        $settings = $this->getSettings();

        $current = $settings['widgets'] ?? [];
        $rebuilt = [];

        foreach ($widgets as $item) {
            if (! is_array($item)) {
                continue;
            }

            $actualKey = $item['key'] ?? null;

            if (! $actualKey) {
                continue;
            }

            $existing = $current[$actualKey] ?? [];

            $rebuilt[$actualKey] = array_merge($existing, [
                'label' => $this->resolveLabel($actualKey, $item, $existing),
                'enabled' => array_key_exists('enabled', $item)
                    ? (bool) $item['enabled']
                    : false,
                'order' => $this->resolveOrder($item, $existing, count($rebuilt) + 1),
            ]);
        }

        if ($rebuilt !== []) {
            $settings['widgets'] = $rebuilt + Arr::except($current, array_keys($rebuilt));
        }

        return $this->saveSettings($settings);
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

        if (array_key_exists($widget->key, $settings['widgets'] ?? [])) {
            unset($settings['widgets'][$widget->key]);
            $this->saveSettings($settings);
        }
    }

    public function getWidgetConfig(string $key): array
    {
        $settings = $this->getSettings();

        if (! array_key_exists($key, $settings['widgets'] ?? [])) {
            return [
                'enabled' => true,
                'order' => 999,
            ];
        }

        return $settings['widgets'][$key];
    }

    public function applyForUserWidgets(array $widgets, Collection $widgetCollection): array
    {
        $settings = $this->getSettings();
        $widgetsConfig = $settings['widgets'];
        $allowOverrides = (bool) Arr::get($settings, 'allow_user_overrides', true);

        $widgetCollectionById = $widgetCollection->keyBy('id');
        $widgetCollectionByName = $widgetCollection->keyBy('name');

        $filteredWidgets = array_values(array_filter(
            $widgets,
            function (array $widget) use ($widgetsConfig, $widgetCollectionById) {
                $model = $widgetCollectionById->get($widget['id'] ?? null);

                if (! $model) {
                    return true;
                }

                $config = $widgetsConfig[$model->name] ?? ['enabled' => true];

                return Arr::get($config, 'enabled', true);
            }
        ));

        $orders = [];

        foreach ($filteredWidgets as $widget) {
            $model = $widgetCollectionById->get($widget['id'] ?? null);

            if (! $model) {
                continue;
            }

            $orders[$model->name] = Arr::get($widgetsConfig[$model->name] ?? [], 'order', 999);
        }

        $sortWidgets = function (array $items) use ($orders, $widgetCollectionById) {
            usort($items, function (array $first, array $second) use ($orders, $widgetCollectionById) {
                $firstModel = $widgetCollectionById->get($first['id'] ?? null);
                $secondModel = $widgetCollectionById->get($second['id'] ?? null);

                $firstOrder = $firstModel ? ($orders[$firstModel->name] ?? 999) : 999;
                $secondOrder = $secondModel ? ($orders[$secondModel->name] ?? 999) : 999;

                return $firstOrder <=> $secondOrder;
            });

            return $items;
        };

        $widgetItems = $sortWidgets(array_values(array_filter(
            $filteredWidgets,
            fn ($widget) => Arr::get($widget, 'type') === 'widget'
        )));

        $statItems = $sortWidgets(array_values(array_filter(
            $filteredWidgets,
            fn ($widget) => Arr::get($widget, 'type') !== 'widget'
        )));

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
            if (! array_key_exists($widgetKey, $normalized)) {
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
            $existing = $normalized[$widget->key] ?? [];
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

    public function tablesReady(): bool
    {
        return $this->hasSettingsTable();
    }

    public function customWidgetsReady(): bool
    {
        return $this->hasCustomWidgetsTable();
    }

    protected function saveSettings(array $settings): bool
    {
        if (! $this->hasSettingsTable()) {
            return false;
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

        return true;
    }

    protected function sortWidgets(array $widgets): array
    {
        uasort($widgets, fn ($a, $b) => ($a['order'] ?? 999) <=> ($b['order'] ?? 999));

        return $widgets;
    }

    protected function resolveLabel(string $key, array $submitted, array $existing): string
    {
        $label = $submitted['label'] ?? null;

        if (is_string($label) && $label !== '') {
            return $label;
        }

        if (isset($existing['label']) && $existing['label'] !== '') {
            return (string) $existing['label'];
        }

        return $this->makeLabel($key);
    }

    protected function resolveOrder(array $submitted, array $existing, int $fallback): int
    {
        if (array_key_exists('order', $submitted) && $submitted['order'] !== '' && $submitted['order'] !== null) {
            return (int) $submitted['order'];
        }

        if (isset($existing['order'])) {
            return (int) $existing['order'];
        }

        return $fallback;
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
