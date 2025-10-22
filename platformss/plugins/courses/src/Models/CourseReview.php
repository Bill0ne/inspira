<?php

namespace Botble\Courses\Models;

use Botble\Base\Casts\SafeContent;
use Botble\Base\Models\BaseModel;
use Botble\Hotel\Enums\ReviewStatusEnum;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseReview extends BaseModel
{
    protected $table = 'course_reviews';

    protected $fillable = [
        'customer_id',
        'course_id',
        'star',
        'content',
        'status',
    ];

    protected $casts = [
        'star' => 'int',
        'status' => ReviewStatusEnum::class,
        'content' => SafeContent::class,
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id')->withDefault();
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(\Botble\Hotel\Models\Customer::class, 'customer_id')->withDefault();
    }
}
