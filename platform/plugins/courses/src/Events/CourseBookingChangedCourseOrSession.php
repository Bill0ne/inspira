<?php

namespace Botble\Courses\Events;

use Botble\Base\Events\Event;
use Botble\Courses\Models\CourseBooking;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CourseBookingChangedCourseOrSession extends Event
{
    use Dispatchable, SerializesModels;

    public function __construct(public CourseBooking $courseBooking)
    {
    }
}
