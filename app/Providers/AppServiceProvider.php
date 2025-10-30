<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (
            class_exists(\Theme\Riorelax\Helpers\FilterHelper::class) &&
            ! class_exists('Theme\\Rlorenak\\Helpers\\FilterHelper', false)
        ) {
            class_alias(
                \Theme\Riorelax\Helpers\FilterHelper::class,
                'Theme\\Rlorenak\\Helpers\\FilterHelper'
            );
        }
    }
}
