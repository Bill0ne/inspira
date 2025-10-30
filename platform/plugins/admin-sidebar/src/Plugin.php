<?php

namespace Botble\AdminSidebar;

use Botble\Base\Facades\DashboardMenu;
use Botble\PluginManagement\Abstracts\PluginOperationAbstract;

class Plugin extends PluginOperationAbstract
{
    public static function activated(): void
    {
        static::flushMenuCache();
    }

    public static function deactivated(): void
    {
        static::flushMenuCache();
    }

    public static function remove(): void
    {
        static::flushMenuCache();
    }

    protected static function flushMenuCache(): void
    {
        DashboardMenu::default()->clearCaches();
    }
}
