<?php

namespace Botble\Courses\Http\Requests;

use Botble\Base\Facades\BaseHelper;
use Botble\Hotel\Facades\HotelHelper;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class CourseCheckoutRequest extends Request
{
    public function rules(): array
    {
        $dateFormat = HotelHelper::getDateFormat();

        $rules = [
            'course_id' => ['required', 'exists:courses,id'],
            'session_id' => ['required', 'exists:course_sessions,id'],
            'first_name' => ['required', 'string', 'max:120'],
            'last_name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:120'],
            'phone' => ['required', ...explode('|', BaseHelper::getPhoneValidationRule())],
            'zip' => ['nullable', 'string', 'max:10'],
            'address' => ['nullable', 'string', 'max:400'],
            'city' => ['nullable', 'string', 'max:60'],
            'state' => ['nullable', 'string', 'max:60'],
            'country' => ['nullable', 'string', 'max:60'],
            'requests' => ['nullable', 'string', 'max:10000'],
            'terms_conditions' => ['accepted:1'],
            'register_customer' => ['nullable'],
            'password' => ['nullable', 'required_if:register_customer,1', 'min:6'],
            'password_confirmation' => ['nullable', 'required_if:register_customer,1', 'same:password'],
            'customer_card_id' => ['nullable', 'integer', 'exists:ht_customer_cards,id'],
        ];

        if (is_plugin_active('payment')) {
            $rules['payment_method'] = [
                Rule::requiredIf(function () {
                    return (float) $this->input('amount', 0) > 0;
                }),
                'string',
            ];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'payment_method.required' => trans('plugins/payment::payment.payment_method_required'),
        ];
    }
}
