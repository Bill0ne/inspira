<?php

namespace Botble\Community\Providers;

use Botble\Base\Facades\DashboardMenu;
use Botble\Base\Supports\DashboardMenuItem;
use Botble\Base\Supports\ServiceProvider;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\Community\Models\CommunityMember;
use Botble\Slug\Facades\SlugHelper;

class CommunityServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function boot(): void
    {
        $this
            ->setNamespace('plugins/community')
            ->loadAndPublishConfigurations(['permissions'])
            ->loadAndPublishTranslations()
            ->loadRoutes()
            ->loadMigrations();

        SlugHelper::registering(function (): void {
            SlugHelper::registerModule(CommunityMember::class, fn () => trans('plugins/community::community.members'));
            SlugHelper::setPrefix(CommunityMember::class, 'community');
        });

        DashboardMenu::default()->beforeRetrieving(function (): void {
            DashboardMenu::make()
                ->registerItem(
                    DashboardMenuItem::make()
                        ->id('cms-plugins-community')
                        ->priority(6)
                        ->name('plugins/community::community.menu')
                        ->icon('ti ti-users-group')
                        ->route('community.index')
                        ->permissions(['community.index'])
                );
        });
    }
}
