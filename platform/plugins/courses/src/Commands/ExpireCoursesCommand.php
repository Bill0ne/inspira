<?php

namespace Botble\Courses\Commands;

use Botble\Courses\Supports\CourseStatusManager;
use Illuminate\Console\Command;

class ExpireCoursesCommand extends Command
{
    protected $signature = 'courses:expire';

    protected $description = 'Mark courses as expired when their start or session dates are in the past.';

    public function handle(): int
    {
        $expired = CourseStatusManager::expireEligibleCourses();

        $this->info("Courses marked as expired: {$expired}");

        return self::SUCCESS;
    }
}
