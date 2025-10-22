<?php

namespace Botble\Courses\Providers;

use Botble\Base\Facades\Assets;
use Botble\Base\Facades\EmailHandler;
use Botble\Base\Supports\ServiceProvider;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\Base\Facades\DashboardMenu;
use Botble\Courses\Models\Course;
use Botble\Courses\Models\Instructor;
use Botble\Courses\Models\CourseCategory;
use Botble\Base\Supports\DashboardMenuItem;
use Botble\Courses\Facades\CourseHelper;
use Botble\LanguageAdvanced\Supports\LanguageAdvancedManager;
use Botble\Slug\Facades\SlugHelper;
use Botble\Theme\Facades\SiteMapManager;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Routing\Router;

class CourseServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register(): void
    {
        /**
         * @var Router $router
         */
        $router = $this->app['router'];

        $aliasLoader = AliasLoader::getInstance();

        if (! class_exists('CourseHelper')) {
            $aliasLoader->alias('CourseHelper', CourseHelper::class);
        }
    }

    public function boot(): void
    {
        $this
            ->setNamespace('plugins/courses')
            ->loadHelpers()
            ->loadAndPublishConfigurations(['permissions', 'email'])
            ->loadAndPublishTranslations()
            ->loadRoutes()
            ->loadAndPublishViews()
            ->loadMigrations()
            ->publishAssets();

        SlugHelper::registering(function (): void {
            SlugHelper::registerModule(Course::class, fn () => trans('plugins/courses::courses.course.name'));
            SlugHelper::setPrefix(Course::class, 'kurse');
        });

        SlugHelper::registering(function (): void {
            SlugHelper::registerModule(CourseCategory::class, fn () => trans('plugins/courses::courses.course-category.name'));
            SlugHelper::setPrefix(CourseCategory::class, 'kurse-kategorien');
        });

        SlugHelper::registering(function (): void {
            SlugHelper::registerModule(Instructor::class, fn () => trans('plugins/courses::courses.instructor.name'));
            SlugHelper::setPrefix(Instructor::class, 'trainerinnen');
        });

        $this->app->register(HookServiceProvider::class);
        $this->app->register(EventServiceProvider::class);

        $this->app['events']->listen(RouteMatched::class, function (): void {
            EmailHandler::addTemplateSettings(COURSE_MODULE_SCREEN_NAME, config('plugins.courses.email'));

            Assets::addScripts(['booking-create'])
                ->addStylesDirectly('vendor/core/plugins/hotel/css/hotel.css');
        });

        if (defined('LANGUAGE_ADVANCED_MODULE_SCREEN_NAME')) {
            \Botble\LanguageAdvanced\Supports\LanguageAdvancedManager::registerModule(Course::class, ['name']);
            \Botble\LanguageAdvanced\Supports\LanguageAdvancedManager::registerModule(Instructor::class, ['name']);
            \Botble\LanguageAdvanced\Supports\LanguageAdvancedManager::registerModule(CourseCategory::class, ['name']);
        }


        DashboardMenu::default()->beforeRetrieving(function (): void {
            DashboardMenu::make()
                ->registerItem(
                    DashboardMenuItem::make()
                        ->id('cms-plugins-courses')
                        ->priority(5)
                        ->name('plugins/courses::courses.manage')
                        ->icon('ti ti-school')
                )
                ->registerItem(
                    DashboardMenuItem::make()
                        ->id('cms-plugins-courses-course')
                        ->priority(1)
                        ->parentId('cms-plugins-courses')
                        ->name('plugins/courses::courses.course.name')
                        ->icon('ti ti-book')
                        ->route('course.index')
                )
                ->registerItem(
                    DashboardMenuItem::make()
                        ->id('cms-plugins-courses-instructors')
                        ->priority(2)
                        ->parentId('cms-plugins-courses')
                        ->name('plugins/courses::courses.instructor.name')
                        ->icon('ti ti-user')
                        ->route('instructor.index')
                )
                ->registerItem(
                    DashboardMenuItem::make()
                        ->id('cms-plugins-courses-categories')
                        ->priority(3)
                        ->parentId('cms-plugins-courses')
                        ->name('plugins/courses::courses.course-category.name')
                        ->icon('ti ti-category')
                        ->route('course-category.index')
                )
                ->registerItem(
                    DashboardMenuItem::make()
                        ->id('cms-plugins-courses-bookings')
                        ->priority(4)
                        ->parentId('cms-plugins-courses')
                        ->name('plugins/courses::courses.course.booking')
                        ->icon('ti ti-calendar')
                        ->route('course-booking.index')
                )
                ->registerItem(
                    DashboardMenuItem::make()
                        ->id('cms-plugins-courses-sessions')
                        ->priority(5)
                        ->parentId('cms-plugins-courses')
                        ->name('plugins/courses::courses.course-session.name')
                        ->icon('ti ti-clock')
                        ->route('course-session.index')
                )
                ->registerItem(
                    DashboardMenuItem::make()
                        ->id('cms-plugins-courses-reviews')
                        ->priority(4)
                        ->parentId('cms-plugins-courses')
                        ->name('plugins/courses::courses.course.review')
                        ->icon('ti ti-star')
                        ->route('course-review.index')
                )
            ;
        });

        if (defined('LANGUAGE_MODULE_SCREEN_NAME') && defined('LANGUAGE_ADVANCED_MODULE_SCREEN_NAME')) {
            LanguageAdvancedManager::registerModule(COURSE::class, [
                'name',
                'description',
            ]);
        }

        SiteMapManager::registerKey(['courses']);
    }
}
