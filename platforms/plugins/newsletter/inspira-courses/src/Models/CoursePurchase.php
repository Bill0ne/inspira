<?php

namespace Botble\InspiraCourses\Models;

use Illuminate\Database\Eloquent\Model;

class CoursePurchase extends Model
{
    protected $table = 'insp_course_purchases';

    protected $fillable = [
        'session_id',
        'customer_id',
        'qty',
        'amount',
        'currency',
        'transaction_id',
        'payment_id',
        'status',
    ];

    public function session()
    {
        return $this->belongsTo(CourseSession::class);
    }
}
