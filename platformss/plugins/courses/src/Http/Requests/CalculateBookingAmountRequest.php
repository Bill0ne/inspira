<?php

namespace Botble\Courses\Http\Requests;

use Botble\Support\Http\Requests\Request;

class CalculateBookingAmountRequest extends Request
{
    public function rules(): array
    {
        $rules = [
            'course_id' => ['required', 'exists:courses,id'],
        ];

        return $rules;
    }
}
