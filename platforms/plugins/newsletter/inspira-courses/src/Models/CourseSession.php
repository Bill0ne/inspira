<?php

namespace Botble\InspiraCourses\Models;

use Illuminate\Database\Eloquent\Model;

class CourseSession extends Model
{
    protected $table = 'insp_course_sessions';

    protected $fillable = [
        'course_id',
        'starts_at',
        'ends_at',
        'location',
        'capacity',
        'seats_sold',
        'price',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at'   => 'datetime',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function remainingSeats(): int
    {
        $cap = (int) ($this->capacity ?? 0);
        $sold = (int) ($this->seats_sold ?? 0);
        $left = $cap - $sold;
        return $left > 0 ? $left : 0;
    }
}
