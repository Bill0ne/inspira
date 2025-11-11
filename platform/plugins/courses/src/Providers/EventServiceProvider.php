<?php

namespace Botble\Courses\Providers;

use Botble\Courses\Events\CourseBookingCreated;
use Botble\Courses\Events\CourseBookingChangedStatus;
use Botble\Courses\Listeners\GenerateCourseInvoiceListener;
use Botble\Courses\Listeners\SendConfirmationEmail;
use Botble\Courses\Listeners\SendCourseStatusChangedNotificationListener;
use Botble\Courses\Listeners\SendCourseOrSessionChangedEmailListener;
use Botble\Courses\Events\CourseBookingChangedCourseOrSession;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        CourseBookingCreated::class => [
            GenerateCourseInvoiceListener::class,
            SendConfirmationEmail::class,
        ],
        CourseBookingChangedStatus::class => [
            SendCourseStatusChangedNotificationListener::class,
        ],
        CourseBookingChangedCourseOrSession::class => [
            SendCourseOrSessionChangedEmailListener::class,
        ],
    ];
}
