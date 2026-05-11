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

    protected function prepareForValidation(): void
    {
        // Bei type=room → course_id leeren, bei type=course → room_id leeren.
        // Verhindert, dass durch das disabled-Feld im Frontend versehentlich
        // beide IDs gesendet werden (würde den DB-CHECK-Constraint verletzen).
        $type = $this->input('type');

        if ($type === 'room') {
            $this->merge(['course_id' => null]);
        } elseif ($type === 'course') {
            $this->merge(['room_id' => null]);
        }
    }
}
