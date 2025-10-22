<?php

namespace Botble\Courses\Http\Requests;

use Botble\Hotel\Facades\HotelHelper;
use Botble\Payment\Enums\PaymentMethodEnum;
use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\Support\Http\Requests\Request;

class CreateCourseBookingRequest extends Request
{
    public function rules(): array
    {
        return [
            'course_id' => ['required', 'exists:courses,id'],
            'status' => ['required', 'string'],
            'customer_id' => ['nullable', 'exists:ht_customers,id'],
            'payment_method' => ['required', 'string', 'in:' . implode(',', PaymentMethodEnum::values())],
            'payment_status' => ['required', 'string', 'in:' . implode(',', PaymentStatusEnum::values())],
            'transaction_id' => ['nullable', 'string', 'max:60'],
        ];
    }
}
