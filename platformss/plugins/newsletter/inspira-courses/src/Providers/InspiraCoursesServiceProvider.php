<?php

namespace Botble\InspiraCourses\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class InspiraCoursesServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../../routes/web.php');
        $this->loadRoutesFrom(__DIR__ . '/../../routes/admin.php');
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'inspira-courses');
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

        View::composer('*', function ($view) {
            $view->with('isLoggedIn', auth('customer')->check() || auth()->check());
        });

        if (function_exists('dashboard_menu')) {
            dashboard_menu()->registerItem([
                'id'          => 'cms-plugins-inspira-courses',
                'priority'    => 5,
                'parent_id'   => null,
                'name'        => 'Inspira Kurse',
                'icon'        => 'ti ti-ticket',
                'url'         => route('inspira-courses.courses.index'),
                'permissions' => [],
            ])->registerItem([
                'id'          => 'cms-plugins-inspira-courses-courses',
                'priority'    => 1,
                'parent_id'   => 'cms-plugins-inspira-courses',
                'name'        => 'Kurse',
                'icon'        => 'ti ti-book',
                'url'         => route('inspira-courses.courses.index'),
                'permissions' => [],
            ])->registerItem([
                'id'          => 'cms-plugins-inspira-courses-sessions',
                'priority'    => 2,
                'parent_id'   => 'cms-plugins-inspira-courses',
                'name'        => 'Termine',
                'icon'        => 'ti ti-calendar-event',
                'url'         => route('inspira-courses.sessions.index'),
                'permissions' => [],
            ])->registerItem([
                'id'          => 'cms-plugins-inspira-courses-instructors',
                'priority'    => 3,
                'parent_id'   => 'cms-plugins-inspira-courses',
                'name'        => 'Kurshalter',
                'icon'        => 'ti ti-user',
                'url'         => route('inspira-courses.instructors.index'),
                'permissions' => [],
            ])->registerItem([
                'id'          => 'cms-plugins-inspira-courses-purchases',
                'priority'    => 4,
                'parent_id'   => 'cms-plugins-inspira-courses',
                'name'        => 'Teilnehmer / Käufe',
                'icon'        => 'ti ti-users',
                'url'         => route('inspira-courses.purchases.index'),
                'permissions' => [],
            ]);
        }
    }
}
