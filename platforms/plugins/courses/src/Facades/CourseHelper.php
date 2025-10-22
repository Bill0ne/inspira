<?php

namespace Botble\Courses\Facades;

use Botble\Courses\Supports\CourseSupport;
use Illuminate\Support\Facades\Facade;

/**
 * @method static bool isReviewEnabled()
 * @see \Botble\Courses\Supports\CourseSupport
 */
class CourseHelper extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return CourseSupport::class;
    }
}
