<?php

namespace Botble\Courses;

use Illuminate\Support\Facades\Schema;
use Botble\PluginManagement\Abstracts\PluginOperationAbstract;

class Plugin extends PluginOperationAbstract
{
    public static function remove(): void
    {
        Schema::dropIfExists('courses');
        Schema::dropIfExists('course_categories');
        Schema::dropIfExists('instructors');
        Schema::dropIfExists('courses_translations');
        Schema::dropIfExists('course_categories_translations');
        Schema::dropIfExists('instructors_translations');
    }
}
