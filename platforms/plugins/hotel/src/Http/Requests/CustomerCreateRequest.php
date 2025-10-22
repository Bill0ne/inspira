<?php

namespace Botble\Hotel\Http\Requests;

use Botble\Base\Facades\BaseHelper;
use Botble\Support\Http\Requests\Request;

class CustomerCreateRequest extends Request
{
    protected function prepareForValidation(): void
    {
        $raw = $this->input('customer_category');

        $this->merge([
            'customer_category' => empty($raw)
                ? 'STANDARD'
                : strtoupper(trim((string) $raw)),
        ]);
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'max:60', 'min:2'],
            'last_name'  => ['required', 'max:60', 'min:2'],
            'email'      => 'required|max:60|min:6|email|unique:ht_customers,email',
            'phone'      => ['nullable', 'string', ...explode('|', BaseHelper::getPhoneValidationRule())],

            'customer_category' => ['nullable', 'string', 'max:191'],

            'password'              => 'required|string|min:6',
            'password_confirmation' => 'required|same:password',
        ];
    }
}
