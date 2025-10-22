<?php

namespace Botble\Courses\Events;

use Botble\Base\Events\Event;
use Botble\Courses\Models\CourseBooking;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CourseBookingCreated extends Event
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public CourseBooking $courseBooking)
    {
    }
}
