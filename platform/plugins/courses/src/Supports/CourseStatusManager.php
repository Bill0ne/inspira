<?php

namespace Botble\Courses\Supports;

use Botble\Courses\Enums\CourseStatusEnum;
use Botble\Courses\Models\Course;
use Carbon\Carbon;

class CourseStatusManager
{
    public static function normalize($status): ?CourseStatusEnum
    {
        if ($status instanceof CourseStatusEnum) {
            return $status;
        }

        if ($status === null) {
            return null;
        }

        return (new CourseStatusEnum())->make($status);
    }

    public static function labels(): array
    {
        return CourseStatusEnum::labels();
    }

    public static function values(): array
    {
        return CourseStatusEnum::values();
    }

    public static function hasExpiredStatusSupport(): bool
    {
        return defined(CourseStatusEnum::class . '::EXPIRED');
    }

    public static function canAutomaticallyExpire(?Course $course): bool
    {
        if (! $course) {
            return false;
        }

        $status = self::normalize($course->status);

        if (! $status) {
            return false;
        }

        return in_array($status->getValue(), [CourseStatusEnum::PUBLISHED, CourseStatusEnum::PENDING], true);
    }

    public static function shouldAutomaticallyExpire(Course $course, ?bool $hasUpcomingSessions = null): bool
    {
        if (! self::canAutomaticallyExpire($course)) {
            return false;
        }

        $hasUpcomingSessions ??= $course->hasUpcomingSessions();

        if ($hasUpcomingSessions) {
            return false;
        }

        $now = Carbon::now();

        if ($course->sessions()->exists()) {
            return true;
        }

        if ($course->end_date instanceof Carbon) {
            return $course->end_date->lte($now);
        }

        if ($course->start_date instanceof Carbon) {
            return $course->start_date->lte($now);
        }

        return false;
    }

    public static function expireCourse(Course $course): void
    {
        $course->forceFill(['status' => CourseStatusEnum::EXPIRED])->saveQuietly();
        $course->setAttribute('status', CourseStatusEnum::EXPIRED());
    }

    public static function refresh(Course $course): void
    {
        $status = self::normalize($course->status);

        if (! $course->exists || ! $status || ! self::hasExpiredStatusSupport()) {
            return;
        }

        if (self::shouldAutomaticallyExpire($course)) {
            self::expireCourse($course);
        }
    }

    public static function getDisplayStatus(Course $course): ?CourseStatusEnum
    {
        $status = self::normalize($course->status);

        if (! $status) {
            return null;
        }

        if (self::shouldAutomaticallyExpire($course)) {
            return CourseStatusEnum::EXPIRED();
        }

        return $status;
    }

    public static function expireEligibleCourses(): int
    {
        $expired = 0;

        Course::withoutEvents(function () use (&$expired): void {
            Course::query()
                ->whereIn('status', [CourseStatusEnum::PUBLISHED, CourseStatusEnum::PENDING])
                ->where(function ($query) {
                    $now = Carbon::now();

                    $query
                        ->where(function ($dateQuery) use ($now) {
                            $dateQuery->whereNotNull('start_date')->where('start_date', '<=', $now);
                        })
                        ->orWhere(function ($dateQuery) use ($now) {
                            $dateQuery->whereNotNull('end_date')->where('end_date', '<=', $now);
                        })
                        ->orWhereHas('sessions', function ($sessionQuery) use ($now) {
                            $sessionQuery
                                ->where('start_date', '<=', $now)
                                ->orWhere('end_date', '<=', $now);
                        });
                })
                ->chunkById(50, function ($courses) use (&$expired) {
                    foreach ($courses as $course) {
                        if (self::shouldAutomaticallyExpire($course)) {
                            self::expireCourse($course);
                            $expired++;
                        }
                    }
                });
        });

        return $expired;
    }
}
