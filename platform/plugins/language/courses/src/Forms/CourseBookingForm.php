<?php

namespace Botble\Courses\Forms;

use Botble\Base\Forms\FieldOptions\StatusFieldOption;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\FormAbstract;
use Botble\Hotel\Enums\BookingStatusEnum;
use Botble\Courses\Http\Requests\UpdateBookingCourseRequest;
use Botble\Courses\Models\CourseBooking;

class CourseBookingForm extends FormAbstract
{
    public function setup(): void
    {
        $this
            ->setupModel(new CourseBooking())
            ->setValidatorClass(UpdateBookingCourseRequest::class)
            ->withCustomFields()
            ->add(
                'status',
                SelectField::class,
                StatusFieldOption::make()
                    ->choices(BookingStatusEnum::labels())
                    ->toArray()
            )
            ->setBreakFieldPoint('status')
            ->addMetaBoxes([
                'information' => [
                    'title' => trans('plugins/courses::courses.course.booking_information'),
                    'content' => view('plugins/courses::booking-info', [
                        'booking' => $this->getModel(),
                    ])->render(),
                    'attributes' => [
                        'style' => 'margin-top: 0',
                    ],
                ],
            ]);
    }
}
