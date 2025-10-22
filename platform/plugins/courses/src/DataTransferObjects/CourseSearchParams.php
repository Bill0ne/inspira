<?php

namespace Botble\Courses\DataTransferObjects;

use Illuminate\Http\Request;

class CourseSearchParams
{
    public function __construct(
        public ?string $keyword = null,
        public ?int $categoryId = null,
        public ?int $instructorId = null,
        public ?float $minPrice = null,
        public ?float $maxPrice = null,
        public ?bool $isFeatured = null,
        public ?string $sortBy = null,
        public string $sortDirection = 'asc',
        public int $page = 1,
        public int $perPage = 10,
        public array $with = [],
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            keyword: $data['keyword'] ?? null,
            categoryId: $data['category_id'] ?? null,
            instructorId: $data['instructor_id'] ?? null,
            minPrice: isset($data['min_price']) && $data['min_price'] !== '' ? (float) $data['min_price'] : null,
            maxPrice: isset($data['max_price']) && $data['max_price'] !== '' ? (float) $data['max_price'] : null,
            isFeatured: $data['is_featured'] ?? null,
            sortBy: $data['sort_by'] ?? null,
            sortDirection: $data['sort_direction'] ?? 'asc',
            page: $data['page'] ?? 1,
            perPage: $data['per_page'] ?? 10,
            with: $data['with'] ?? [],
        );
    }
}
