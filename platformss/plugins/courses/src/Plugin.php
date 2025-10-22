<?php

namespace Botble\Courses;

use Illuminate\Support\Facades\Schema;
use Botble\PluginManagement\Abstracts\PluginOperationAbstract;

class Plugin extends PluginOperationAbstract
{
    public static function remove(): void
    {
        Schema::dropIfExists('Courses');
        Schema::dropIfExists('Courses_translations');
    }
}
