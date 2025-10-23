<?php

namespace Botble\Modern;

use Botble\Dashboard\Models\DashboardWidget;
use Botble\PluginManagement\Abstracts\PluginOperationAbstract;
use Botble\Widget\Models\Widget;

class Plugin extends PluginOperationAbstract
{
    public static function remove(): void
    {
        Widget::query()
            ->where('widget_id', 'widget_modern_greeting')
            ->each(fn (DashboardWidget $dashboardWidget) => $dashboardWidget->delete());
    }
}
