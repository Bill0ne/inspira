<?php

namespace Botble\Courses\Listeners;

use Botble\Courses\Events\CourseBookingCreated;
use Botble\Courses\Supports\InvoiceHelper;

class GenerateCourseInvoiceListener
{
    public function handle(CourseBookingCreated $event): void
    {
        (new InvoiceHelper())->store($event->courseBooking);
    }
}
