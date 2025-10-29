<?php

namespace Botble\AdminSidebar\Providers;

use Botble\Base\Facades\Assets;
use Botble\Base\Facades\DashboardMenu;
use Botble\Base\Supports\DashboardMenu as DashboardMenuSupport;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AdminSidebarServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function boot(): void
    {
        $this
            ->setNamespace('plugins/admin-sidebar')
            ->loadAndPublishTranslations()
            ->publishAssets();

        $this->reorderDashboardMenu();

        Event::listen(RouteMatched::class, function (): void {
            if (! is_in_admin(true)) {
                return;
            }

            Assets::addStylesDirectly('vendor/core/plugins/admin-sidebar/css/admin-sidebar.css');
            Assets::addScriptsDirectly('vendor/core/plugins/admin-sidebar/js/admin-sidebar.js');
        });
    }

    protected function reorderDashboardMenu(): void
    {
        DashboardMenu::default()->beforeRetrieving(function (DashboardMenuSupport $menu): void {
            foreach ($this->menuGroups() as $groupId => $group) {
                $menu->registerItem([
                    'id' => $groupId,
                    'priority' => $group['priority'],
                    'name' => $group['label'],
                    'title' => trans($group['label']),
                    'icon' => false,
                    'class' => $group['class'],
                ]);

                foreach ($group['items'] as $itemId => $priority) {
                    if (! $menu->hasItem($itemId)) {
                        continue;
                    }

                    $item = $menu->getItemById($itemId);

                    if (! $item) {
                        continue;
                    }

                    $menu->registerItem(array_merge($item, [
                        'parent_id' => $groupId,
                        'priority' => $priority,
                    ]));
                }
            }

            $menu->removeItem('cms-core-system');
        });
    }

    protected function menuGroups(): array
    {
        return [
            'cms-custom-inspira-management' => [
                'label' => 'plugins/admin-sidebar::menu.groups.inspira_management',
                'priority' => -9000,
                'class' => 'menu-section menu-section--inspira menu-section--management',
                'items' => [
                    'cms-plugins-courses' => 0,
                    'cms-plugins-hotel' => 10,
                    'cms-plugins-booking' => 20,
                    'cms-plugins-payments' => 30,
                    'cms-plugins-contact' => 40,
                    'cms-plugins-team' => 50,
                    'cms-plugins-price-configurator' => 60,
                ],
            ],
            'cms-custom-website' => [
                'label' => 'plugins/admin-sidebar::menu.groups.website',
                'priority' => -8900,
                'class' => 'menu-section menu-section--website',
                'items' => [
                    'cms-core-page' => 0,
                    'cms-plugins-blog' => 10,
                    'cms-plugins-testimonial' => 20,
                    'cms-plugins-gallery' => 30,
                    'cms-plugins-simple-slider' => 40,
                    'cms-plugins-faq' => 50,
                    'cms-plugins-newsletter' => 60,
                    'cms-core-media' => 70,
                ],
            ],
            'cms-custom-admin-area' => [
                'label' => 'plugins/admin-sidebar::menu.groups.admin_area',
                'priority' => 9800,
                'class' => 'menu-section menu-section--admin',
                'items' => [
                    'cms-core-appearance' => 0,
                    'cms-core-plugins' => 10,
                    'cms-core-tools' => 20,
                    'cms-core-system-maintenance-mode' => 30,
                    'cms-core-platform-administration' => 35,
                    'cms-core-settings' => 40,
                    'cms-plugins-personal-dashboard' => 50,
                ],
            ],
        ];
    }
}
