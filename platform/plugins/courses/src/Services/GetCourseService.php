<?php

namespace Botble\Courses\Services;

use Botble\Courses\DataTransferObjects\CourseSearchParams;
use Botble\Courses\Models\Course;
use Botble\Courses\Models\CourseSession;
use Botble\Hotel\Enums\BookingStatusEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

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
        $activeBookingStatuses = [
            BookingStatusEnum::PENDING,
            BookingStatusEnum::PROCESSING,
            BookingStatusEnum::COMPLETED,
        ];

        $bookingsCountSql = '(
            select count(*) from course_bookings
            where course_bookings.course_session_id = course_sessions.id
            and course_bookings.status in (?, ?, ?)
        )';

        $course = Course::query()->find($courseId);
        $categoryId = $course?->category_id;
        $instructorId = $course?->instructor_id;

        $priorityExpression = 'CASE';
        $priorityBindings = [];

        if ($categoryId) {
            $priorityExpression .= ' WHEN courses.category_id = ? THEN 0';
            $priorityBindings[] = $categoryId;
        }

        if ($instructorId) {
            $priorityExpression .= ' WHEN courses.instructor_id = ? THEN 1';
            $priorityBindings[] = $instructorId;
        }

        if ($priorityBindings) {
            $priorityExpression .= ' ELSE 2 END';
        } else {
            $priorityExpression = '2';
        }

        $query = Course::query()
            ->select('courses.*')
            ->selectRaw($priorityExpression . ' as sorting_priority', $priorityBindings)
            ->wherePublished()
            ->where('id', '!=', $courseId)
            ->addSelect([
                'next_session_start_date' => CourseSession::query()
                    ->selectRaw('MIN(start_date)')
                    ->whereColumn('course_sessions.course_id', 'courses.id')
                    ->where('start_date', '>=', $now)
                    ->where(function ($availableQuery) use ($bookingsCountSql, $activeBookingStatuses) {
                        $availableQuery
                            ->whereNull('course_sessions.available_seats')
                            ->orWhereRaw(
                                "course_sessions.available_seats > $bookingsCountSql",
                                $activeBookingStatuses
                            );
                    }),
                'remaining_seats_for_sorting' => CourseSession::query()
                    ->selectRaw(
                        "MIN(CASE\n                            WHEN course_sessions.available_seats IS NULL THEN 999999\n                            ELSE course_sessions.available_seats - $bookingsCountSql\n                        END)"
                    )
                    ->whereColumn('course_sessions.course_id', 'courses.id')
                    ->where('course_sessions.start_date', '>=', $now)
                    ->where(function ($availableQuery) use ($bookingsCountSql, $activeBookingStatuses) {
                        $availableQuery
                            ->whereNull('course_sessions.available_seats')
                            ->orWhereRaw(
                                "course_sessions.available_seats > $bookingsCountSql",
                                $activeBookingStatuses
                            );
                    }),
            ])
            ->whereExists(function ($subquery) use ($now, $bookingsCountSql, $activeBookingStatuses) {
                $subquery
                    ->selectRaw('1')
                    ->from('course_sessions')
                    ->whereColumn('course_sessions.course_id', 'courses.id')
                    ->where('course_sessions.start_date', '>=', $now)
                    ->where(function ($availableQuery) use ($bookingsCountSql, $activeBookingStatuses) {
                        $availableQuery
                            ->whereNull('course_sessions.available_seats')
                            ->orWhereRaw(
                                "course_sessions.available_seats > $bookingsCountSql",
                                $activeBookingStatuses
                            );
                    });
            });

        if (! empty($params['with'])) {
            $query->with($params['with']);
        }

        $query
            ->orderBy('sorting_priority')
            ->orderByRaw('CASE WHEN sorting_priority = 2 THEN remaining_seats_for_sorting ELSE 0 END')
            ->orderBy('next_session_start_date')
            ->orderBy('created_at', 'desc');

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
