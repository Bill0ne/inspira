<?php

namespace Botble\Hotel\Http\Requests;

use Botble\Hotel\Facades\HotelHelper;
use Botble\Hotel\Rules\WithinOpeningHours;
use Botble\Support\Http\Requests\Request;

class CalculateBookingAmountRequest extends Request
{
    public function rules(): array
    {
        $dateFormat = HotelHelper::getDateFormat();

        return [
            'room_id' => ['required', 'exists:ht_rooms,id'],
            'slots.*' => [new WithinOpeningHours()],
            'slots.*.start_date' => [
                'required',
                'string',
                'date_format:' . $dateFormat,
                'after_or_equal:today',
            ],
            'slots.*.end_date' => [
                'required',
                'string',
                'date_format:' . $dateFormat,
                'after_or_equal:slots.*.start_date',
            ],
            'services' => ['nullable', 'array'],
        ];
    }
}
