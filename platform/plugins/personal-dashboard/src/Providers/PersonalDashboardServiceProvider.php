<?php

namespace Botble\PersonalDashboard\Providers;

use Botble\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Support\ServiceProvider;

class PersonalDashboardServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register(): void
    {
        $this->app->register(HookServiceProvider::class);
    }

    public function boot(): void
    {
        $this
            ->setNamespace('plugins/personal-dashboard')
            ->loadRoutes()
            ->loadAndPublishViews()
            ->loadAndPublishTranslations();
    }
}
