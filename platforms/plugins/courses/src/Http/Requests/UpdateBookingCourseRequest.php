<?php

namespace Botble\Courses\Http\Requests;

use Botble\Hotel\Enums\BookingStatusEnum;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class UpdateBookingCourseRequest extends Request
{
    public function rules(): array
    {
        return [
            'status' => Rule::in(BookingStatusEnum::values()),
        ];
    }
}
