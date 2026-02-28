<?php

namespace Botble\Contact\Http\Requests;

use Botble\Base\Rules\EmailRule;
use Botble\Base\Rules\PhoneNumberRule;
use Illuminate\Validation\Rule;

class RoomContactRequest extends ContactRequest
{
    public function rules(): array
    {
        $rules = parent::rules();


        $rules['email'] = ['required', new EmailRule(), 'max:80'];
        $rules['phone'] = ['required', new PhoneNumberRule()];
        $rules['company'] = ['nullable', 'string', 'max:120'];
        $rules['room_id'] = ['required', 'integer', Rule::exists('hc_rooms', 'id')];
        $rules['room_name'] = ['required', 'string', 'max:255'];
        $rules['persons'] = ['required', 'integer', 'min:1'];
        $rules['time_from'] = ['required', 'date_format:Y-m-d\TH:i'];
        $rules['time_to'] = ['required', 'date_format:Y-m-d\TH:i', 'after:time_from'];

        return $rules;
    }

    public function attributes(): array
    {
        return array_merge(parent::attributes(), [
            'company' => __('Company'),
            'room_id' => __('Room'),
            'persons' => __('Number of persons'),
            'time_from' => __('From'),
            'time_to' => __('To'),
        ]);
    }
}
