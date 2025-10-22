<?php

namespace Botble\Courses\Services;

use Botble\Courses\DataTransferObjects\CourseSearchParams;
use Botble\Courses\Models\Course;
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

        if ($params->sortBy) {
            switch ($params->sortBy) {
                case 'price':
                case 'name':
                case 'created_at':
                    $query->orderBy($params->sortBy, $params->sortDirection);
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
            }
        } else {
            $query->orderBy('created_at', 'desc');
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
        $query = Course::query()
            ->wherePublished()
            ->where('id', '!=', $courseId);

        $course = Course::query()->find($courseId);
        if ($course && $course->category_id) {
            $query->where('category_id', $course->category_id);
        }

        if (! empty($params['with'])) {
            $query->with($params['with']);
        }

        return $query->limit($limit)->get();
    }
}
