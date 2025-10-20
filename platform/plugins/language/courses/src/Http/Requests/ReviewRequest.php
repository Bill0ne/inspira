<?php

namespace Botble\Courses\Http\Requests;

use Botble\Support\Http\Requests\Request;

class ReviewRequest extends Request
{
    public function rules(): array
    {
        return [
            'course_id' => ['required', 'exists:courses,id'],
            'star' => ['required', 'int', 'min:1', 'max:5'],
            'content' => ['required', 'string', 'min:4', 'max:10000'],
        ];
    }
}
