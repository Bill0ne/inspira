<?php

namespace Botble\InspiraCourses\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $table = 'insp_courses';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'instructor_id',
    ];

    public function sessions()
    {
        return $this->hasMany(CourseSession::class);
    }

    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }
}
