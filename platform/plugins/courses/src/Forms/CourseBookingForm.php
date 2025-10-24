<?php

namespace Botble\Courses\Forms;

use Botble\Base\Facades\Assets;
use Botble\Base\Forms\FieldOptions\StatusFieldOption;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\FormAbstract;
use Botble\Hotel\Enums\BookingStatusEnum;
use Botble\Courses\Http\Requests\UpdateBookingCourseRequest;
use Botble\Courses\Models\CourseBooking;
use Botble\Courses\Models\Course;

class CourseBookingForm extends FormAbstract
{
    public function setup(): void
    {
        Assets::addScriptsDirectly('vendor/core/plugins/courses/js/script.js');
        $course = $this->getModel();

        $this
            ->setupModel(new CourseBooking())
            ->setValidatorClass(UpdateBookingCourseRequest::class)
            ->withCustomFields()
            ->add(
                'course_id',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(__('Kurse'))
                    ->choices(Course::query()->wherePublished()->pluck('name', 'id')->all())
                    ->searchable()
                    ->selected($course->course_id ?? null)
                    ->helperText(__('Ändern Sie den Kurs, wenn der Benutzer den falschen gebucht hat.'))
                    ->attributes(['id' => 'admin_course_id'])
                    ->colspan(2)
            )
            ->add(
                'course_session_id',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(__('Sitzung'))
                    ->choices([])
                    ->searchable()
                    ->helperText(__('Ändern Sie die Sitzungsdaten, wenn der Benutzer das falsche Datum bucht'))
                    ->attributes([
                        'id' => 'admin_session_id',
                        'data-selected' => $course->course_session_id ?? '',
                    ])
                    ->colspan(2)
            )
            ->add(
                'status',
                SelectField::class,
                StatusFieldOption::make()
                    ->choices(BookingStatusEnum::labels())
                    ->toArray()
            )
            ->setBreakFieldPoint('course_id')
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
