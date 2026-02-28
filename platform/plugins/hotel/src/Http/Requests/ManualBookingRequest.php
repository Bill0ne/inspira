<?php

namespace Botble\Hotel\Http\Requests;

use Botble\Support\Http\Requests\Request;

class ManualBookingRequest extends Request
{
    public function rules(): array
    {
        return [
            'type' => ['required', 'in:room,course'],
            'room_id' => ['nullable', 'required_if:type,room', 'integer', 'exists:ht_rooms,id'],
            'course_id' => ['nullable', 'required_if:type,course', 'integer', 'exists:courses,id'],
            'start_at' => ['required', 'date'],
            'end_at' => ['required', 'date', 'after:start_at'],
            'reason' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
