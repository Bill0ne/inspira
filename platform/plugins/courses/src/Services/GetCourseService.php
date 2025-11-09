<?php

namespace Botble\Courses\Services;

use Botble\Courses\DataTransferObjects\CourseSearchParams;
use Botble\Courses\Models\Course;
use Botble\Courses\Models\CourseSession;
use Botble\Hotel\Enums\BookingStatusEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;

class GetCourseService
{
    public function getCourses(CourseSearchParams $params): LengthAwarePaginator
    {
        $query = Course::query()->wherePublished();

        if ($params->keyword) {
            $query->where('name', 'like', "%{$params->keyword}%")
                ->orWhere('description', 'like', "%{$params->keyword}%");
        }

        if ($params->categoryId) {
            $query->where('category_id', $params->categoryId);
        }

        if ($params->instructorId) {
            $query->where('instructor_id', $params->instructorId);
        }

        if ($params->minPrice !== null && $params->maxPrice !== null) {
            $query->whereBetween('price', [$params->minPrice, $params->maxPrice]);
        } elseif ($params->minPrice !== null) {
            $query->where('price', '>=', $params->minPrice);
        } elseif ($params->maxPrice !== null) {
            $query->where('price', '<=', $params->maxPrice);
        }

        if ($params->isFeatured !== null) {
            $query->where('is_featured', $params->isFeatured);
        }

        $query->addSelect([
            'next_session_start_date' => CourseSession::query()
                ->selectRaw('MIN(start_date)')
                ->whereColumn('course_sessions.course_id', 'courses.id')
                ->where('start_date', '>=', now()),
        ]);

        if ($params->sortBy) {
            switch ($params->sortBy) {
                case 'price':
                case 'name':
                case 'created_at':
                    $query->orderBy($params->sortBy, $params->sortDirection);
                    break;
                case 'upcoming_session':
                    $this->orderByUpcomingSession($query, $params->sortDirection);
                    break;
                default:
                    $this->orderByUpcomingSession($query);
            }
        } else {
            $this->orderByUpcomingSession($query);
        }

        if (! empty($params->with)) {
            $query->with($params->with);
        }

        return $query->paginate(
            $params->perPage,
            ['*'],
            'page',
            $params->page
        );
    }

    public function getRelatedCourses(int $courseId, int $limit = 2, array $params = []): Collection
    {
        $now = now();
        $requireAvailableSeats = (bool) Arr::get($params, 'require_available_seats', false);
        $activeBookingStatuses = [
            BookingStatusEnum::PENDING,
            BookingStatusEnum::PROCESSING,
            BookingStatusEnum::COMPLETED,
        ];

        $query = Course::query()
            ->wherePublished()
            ->where('id', '!=', $courseId)
            ->addSelect([
                'next_session_start_date' => CourseSession::query()
                    ->selectRaw('MIN(start_date)')
                    ->whereColumn('course_sessions.course_id', 'courses.id')
                    ->where('start_date', '>=', $now)
                    ->when($requireAvailableSeats, function ($builder) use ($activeBookingStatuses) {
                        $builder->where(function ($availableQuery) use ($activeBookingStatuses) {
                            $availableQuery
                                ->whereNull('course_sessions.available_seats')
                                ->orWhereRaw(
                                    'course_sessions.available_seats > (
                                        select count(*) from course_bookings
                                        where course_bookings.course_session_id = course_sessions.id
                                        and course_bookings.status in (?, ?, ?)
                                    )',
                                    $activeBookingStatuses
                                );
                        });
                    }),
            ])
            ->whereExists(function ($subquery) use ($now, $requireAvailableSeats, $activeBookingStatuses) {
                $subquery
                    ->selectRaw('1')
                    ->from('course_sessions')
                    ->whereColumn('course_sessions.course_id', 'courses.id')
                    ->where('course_sessions.start_date', '>=', $now)
                    ->when($requireAvailableSeats, function ($builder) use ($activeBookingStatuses) {
                        $builder->where(function ($availableQuery) use ($activeBookingStatuses) {
                            $availableQuery
                                ->whereNull('course_sessions.available_seats')
                                ->orWhereRaw(
                                    'course_sessions.available_seats > (
                                        select count(*) from course_bookings
                                        where course_bookings.course_session_id = course_sessions.id
                                        and course_bookings.status in (?, ?, ?)
                                    )',
                                    $activeBookingStatuses
                                );
                        });
                    });
            });

        $course = Course::query()->find($courseId);
        if ($course && $course->category_id) {
            $query->where('category_id', $course->category_id);
        }

        if (! empty($params['with'])) {
            $query->with($params['with']);
        }

        $this->orderByUpcomingSession($query);

        return $query->limit($limit)->get();
    }

    protected function orderByUpcomingSession(Builder $query, string $direction = 'asc'): void
    {
        $query
            ->orderByRaw('CASE WHEN next_session_start_date IS NULL THEN 1 ELSE 0 END')
            ->orderBy('next_session_start_date', $direction)
            ->orderBy('created_at', 'desc');
    }
}
