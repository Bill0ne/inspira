<?php

namespace Botble\InspiraCourses\Models;

use Illuminate\Database\Eloquent\Model;

class Instructor extends Model
{
    protected $table = 'insp_instructors';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'bio',
    ];

    public function courses()
    {
        return $this->hasMany(Course::class);
    }
}
