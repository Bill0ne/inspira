<?php

namespace Botble\Hotel\Models;

use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ManualBooking extends BaseModel
{
    protected $table = 'ht_manual_bookings';

    protected $fillable = [
        'type',
        'room_id',
        'course_id',
        'start_at',
        'end_at',
        'reason',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'room_id')->withDefault();
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(\Botble\Courses\Models\Course::class, 'course_id')->withDefault();
    }
}
