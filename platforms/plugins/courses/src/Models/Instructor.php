<?php

namespace Botble\Courses\Models;

use Botble\Base\Casts\SafeContent;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;
use Illuminate\Support\Str;

class Instructor extends BaseModel
{
    protected $table = 'instructors';

    protected $fillable = [
        'name',
        'photo',
        'email',
        'phone',
        'bio',
        'status',
    ];

    protected $casts = [
        'status' => BaseStatusEnum::class,
        'name' => SafeContent::class,
        'bio'  => SafeContent::class,
    ];

    public function courses()
    {
        return $this->hasMany(Course::class, 'instructor_id');
    }
}
