<?php

namespace Botble\PriceConfigurator\Providers;

use Botble\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Support\ServiceProvider;
use Botble\Base\Facades\DashboardMenu;
use Botble\Base\Facades\BaseHelper;
use Botble\PriceConfigurator\Services\PriceConfigurator;

class PriceConfiguratorServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register(): void
    {
        $this->app->singleton(PriceConfigurator::class, fn () => new PriceConfigurator());
    }

    public function boot(): void
    {
        $this->setNamespace('price-configurator');

        // Konfigurationen laden (inkl. eigener config/price-configurator.php)
        $this->loadAndPublishConfigurations(['permissions', 'price-configurator']);

        // Standard-Ladevorgang
        $this->loadAndPublishTranslations();
        $this->loadAndPublishViews();
        $this->loadMigrations();
        $this->loadRoutes();

        // Bei CLI (artisan) keine Menüs registrieren
        if ($this->app->runningInConsole()) {
            return;
        }

        // Menü registrieren (Hauptpunkt + Unterpunkte)
        $this->app->booted(function () {
            $admin = BaseHelper::getAdminPrefix(); // meist 'admin'

            // HAUPTPUNKT
            DashboardMenu::registerItem([
                'id'          => 'plugins-price-configurator',
                'priority'    => 40,
                'parent_id'   => null,
                'name'        => 'Price Configurator',
                'icon'        => 'ti ti-currency-euro',
                // URL statt route(), damit unabhängig von Routen-Namen
                'url'         => url($admin . '/price-configurator'),
                // Hauptpunkt anzeigen, wenn irgendeine PC-Permission vorhanden ist
                'permissions' => [
                    'pc.admin.index',
                    'pc.tiers.index',
                    'pc.rules.index',
                    'pc.categories.index',
                    'pc.discounts.index',
                ],
            ]);

            // UNTERPUNKTE
            DashboardMenu::registerItem([
                'id'          => 'pc-tiers',
                'priority'    => 41,
                'parent_id'   => 'plugins-price-configurator',
                'name'        => 'Preisstufen',
                'url'         => url($admin . '/price-configurator/tiers'),
                'permissions' => ['pc.tiers.index'],
            ]);

            DashboardMenu::registerItem([
                'id'          => 'pc-rules',
                'priority'    => 42,
                'parent_id'   => 'plugins-price-configurator',
                'name'        => 'Regeln',
                'url'         => url($admin . '/price-configurator/rules'),
                'permissions' => ['pc.rules.index'],
            ]);

            DashboardMenu::registerItem([
                'id'          => 'pc-categories',
                'priority'    => 43,
                'parent_id'   => 'plugins-price-configurator',
                'name'        => 'Kundenkategorien',
                'url'         => url($admin . '/price-configurator/categories'),
                'permissions' => ['pc.categories.index'],
            ]);

            DashboardMenu::registerItem([
                'id'          => 'pc-discounts',
                'priority'    => 44,
                'parent_id'   => 'plugins-price-configurator',
                'name'        => 'Mengen- & Spezialrabatte',
                'url'         => url($admin . '/price-configurator/discounts'),
                'permissions' => ['pc.discounts.index'],
            ]);
        });
    }
}
