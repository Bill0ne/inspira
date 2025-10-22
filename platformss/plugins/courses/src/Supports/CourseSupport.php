<?php

namespace Botble\Courses\Supports;

class CourseSupport
{
    public function isReviewEnabled(): bool
    {
        return (bool) setting('course_enable_review_room', 1);
    }
}
