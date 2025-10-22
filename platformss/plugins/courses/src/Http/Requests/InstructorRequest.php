<?php

namespace Botble\Courses\Http\Requests;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Rules\EmailRule;
use Botble\Base\Rules\PhoneNumberRule;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class InstructorRequest extends Request
{
    public function rules(): array
    {
        return [
            'name'   => ['required', 'string', 'max:220'],
            'email' => ['required', 'max:120', 'min:6', new EmailRule()],
            'phone' => ['nullable', new PhoneNumberRule()],
            'photo'  => ['nullable', 'string', 'max:255'],
            'bio'    => ['nullable', 'string'],
            'status' => Rule::in(BaseStatusEnum::values()),
        ];
    }
}
