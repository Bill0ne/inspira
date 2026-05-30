<?php

namespace Botble\Hotel\Http\Requests;

use Botble\Support\Http\Requests\Request;

class ManualBookingRequest extends Request
{
    public function rules(): array
    {
        return [
            'type' => ['required', 'in:room,course'],
            // required_if behandelt ein leeres Array als „leer" und erzwingt so
            // mindestens einen Raum bei type=room. Kein min:1, damit type=course
            // (room_id = []) nicht fälschlich abgelehnt wird.
            'room_id' => ['nullable', 'required_if:type,room', 'array'],
            'room_id.*' => ['integer', 'exists:ht_rooms,id'],
            'course_id' => ['nullable', 'required_if:type,course', 'integer', 'exists:courses,id'],
            'start_at' => ['required', 'date'],
            'end_at' => ['required', 'date', 'after:start_at'],
            'reason' => ['nullable', 'string', 'max:1000'],
        ];
    }

    protected function prepareForValidation(): void
    {
        // Bei type=room → course_id leeren, bei type=course → Räume leeren.
        // Verhindert, dass durch das disabled-Feld im Frontend versehentlich
        // beide Ziele gesendet werden (würde den DB-CHECK-Constraint verletzen).
        // room_id wird als Array (Mehrfachauswahl) erwartet und auf eindeutige
        // Integer normalisiert.
        $type = $this->input('type');

        if ($type === 'room') {
            $roomIds = array_values(array_unique(array_filter(
                array_map('intval', (array) $this->input('room_id', [])),
                fn (int $id): bool => $id > 0
            )));

            $this->merge(['course_id' => null, 'room_id' => $roomIds]);
        } elseif ($type === 'course') {
            $this->merge(['room_id' => []]);
        }
    }
}
