<?php

namespace Botble\Courses\Http\Requests;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Rules\OnOffRule;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class CourseRequest extends Request
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:220'],
            'description'     => ['nullable', 'string'],
            'price'           => ['required', 'numeric', 'min:0'],
            'duration'        => ['required', 'string', 'max:120'],
            'start_date'      => ['required'],
            'end_date'        => ['required', 'after_or_equal:start_date'],
            'thumbnail'       => ['nullable', 'string'],
            'category_id'     => ['required'],
            'instructor_id'   => ['required'],
            'unlimited_seats' => [new OnOffRule()],
            'is_featured' => [new OnOffRule()],
            'number_of_seats' => [
                'required_if:unlimited_seats,0',
                'nullable',
                'integer',
                'min:1',
            ],
            'is_recurring'       => [new OnOffRule()],
            'recurring_type'     => ['nullable', 'required_if:is_recurring,1', Rule::in(['daily', 'weekly', 'monthly'])],
            'recurring_interval' => ['nullable', 'required_if:is_recurring,1', 'integer', 'min:1'],
            'recurring_until'    => ['nullable'],
            'status' => Rule::in(BaseStatusEnum::values()),
            'tax_id' => ['required', 'string', 'exists:ht_taxes,id'],
        ];
    }
}
