<?php

namespace Botble\Courses\Http\Controllers;

use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\Courses\Http\Requests\CourseRequest;
use Botble\Courses\Models\Course;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Courses\Tables\CourseTable;
use Botble\Courses\Forms\CourseForm;

class CourseController extends BaseController
{
    public function __construct()
    {
        $this
            ->breadcrumb()
            ->add(trans(trans('plugins/courses::courses.course.name')), route('course.index'));
    }

    public function index(CourseTable $table)
    {
        $this->pageTitle(trans('plugins/courses::courses.course.name'));

        return $table->renderTable();
    }

    public function create()
    {
        $this->pageTitle(trans('plugins/courses::courses.course.create'));

        return CourseForm::create()->renderForm();
    }

    public function store(CourseRequest $request)
    {
        $form = CourseForm::create()->setRequest($request);

        $form->save();

        /** @var Course $course */
        $course = $form->getModel();

        $this->generateSessions($course);

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('course.index'))
            ->setNextUrl(route('course.edit', $course->getKey()))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(Course $course)
    {
        $this->pageTitle(trans('core/base::forms.edit_item', ['name' => $course->name]));

        return CourseForm::createFromModel($course)->renderForm();
    }

    public function update(Course $course, CourseRequest $request)
    {
        $newRecurringUntil = $request->input('recurring_until');
        $oldRecurringUntil = $course->recurring_until;

        if ($oldRecurringUntil && $newRecurringUntil && $newRecurringUntil < $oldRecurringUntil) {
            $sessionsWithBookings = $course->sessions()
                ->where('start_date', '>', $newRecurringUntil)
                ->whereHas('bookings')
                ->count();

            if ($sessionsWithBookings > 0) {
                return $this
                    ->httpResponse()
                    ->setError()
                    ->setPreviousUrl(route('course.edit', $course->getKey()))
                    ->setMessage('Cannot reduce recurring date. There are ' . $sessionsWithBookings . ' sessions with existing bookings. Please cancel them first.')
                    ->toResponse($request);
            }
        }

        CourseForm::createFromModel($course)
            ->setRequest($request)
            ->save();

        $course->refresh();

        $this->generateSessions($course);

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('course.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(Course $course)
    {
        return DeleteResourceAction::make($course);
    }

    protected function generateSessions(Course $course): void
    {
        $dates = $course->isRecurring() ? $course->generateRecurringDates() : [$course->start_date];

        $existingSessions = $course->sessions()->with('bookings')->get();

        $existingDates = $existingSessions->pluck('start_date')->map(fn($d) => $d->toDateString())->toArray();

        foreach ($existingSessions as $session) {
            if ($session->bookings->count() > 0) {
                if (!in_array($session->start_date->toDateString(), array_map(fn($d) => $d->toDateString(), $dates))) {
                    $session->bookings()->update(['in_recurrence' => false]);
                    $session->update(['in_recurrence' => false]);
                }
                continue;
            }

            if (!in_array($session->start_date->toDateString(), array_map(fn($d) => $d->toDateString(), $dates))) {
                $session->delete();
            } else {
                $session->update([
                    'available_seats' => $course->unlimited_seats ? null : $course->number_of_seats,
                ]);
            }
        }

        foreach ($dates as $date) {
            if (!in_array($date->toDateString(), $existingDates)) {
                $course->sessions()->create([
                    'start_date' => $date,
                    'end_date' => $course->end_date ? $date->copy()->setTimeFrom($course->end_date) : null,
                    'available_seats' => $course->unlimited_seats ? null : $course->number_of_seats,
                    'in_recurrence' => true,
                ]);
            }
        }
    }

    public function getDuplicate(Course $course)
    {
        $duplicatedCourse = Course::query()->create([
            'name'             => $course->name . ' (Duplicate)',
            'category_id'      => $course->category_id,
            'instructor_id'    => $course->instructor_id,
            'description'      => $course->description,
            'price'            => $course->price,
            'unlimited_seats'  => $course->unlimited_seats,
            'number_of_seats'  => $course->number_of_seats,
            'duration'         => $course->duration,
            'start_date'       => $course->start_date,
            'end_date'         => $course->end_date,
            'is_recurring'     => $course->is_recurring,
            'recurring_type'   => $course->recurring_type,
            'recurring_interval' => $course->recurring_interval,
            'recurring_until'  => $course->recurring_until,
            'thumbnail'        => $course->thumbnail,
            'is_featured'      => $course->is_featured,
            'status'           => $course->status,
        ]);

        return $this->httpResponse()
            ->setPreviousRoute('course.edit', $course->getKey())
            ->setNextRoute('course.edit', $duplicatedCourse->getKey())
            ->setMessage(trans('core/acl::permissions.duplicated_success'));
    }

}
