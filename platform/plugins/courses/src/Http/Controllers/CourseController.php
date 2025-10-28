<?php

namespace Botble\Courses\Http\Controllers;

use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\Courses\Http\Requests\CourseRequest;
use Botble\Courses\Models\Course;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Courses\Tables\CourseTable;
use Botble\Courses\Forms\CourseForm;
use Botble\Courses\Models\CourseSession;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

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
        $newStartDate = $request->input('start_date');
        $oldStartDate = $course->start_date;

        if ($oldRecurringUntil && $newRecurringUntil && $newRecurringUntil < $oldRecurringUntil) {
            $sessionsWithBookings = $course->sessions()
                ->where('is_manual', false)
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

            $sessionsToDelete = $course->sessions()
                ->where('is_manual', false)
                ->where('start_date', '>', $newRecurringUntil)
                ->doesntHave('bookings')
                ->get();

            foreach ($sessionsToDelete as $session) {
                $session->delete();
            }
        }

        if ($oldStartDate && $newStartDate && $newStartDate > $oldStartDate) {
            $sessionsWithBookings = $course->sessions()
                ->where('is_manual', false)
                ->where('start_date', '<', $newStartDate)
                ->whereHas('bookings')
                ->count();

            if ($sessionsWithBookings > 0) {
                return $this
                    ->httpResponse()
                    ->setError()
                    ->setPreviousUrl(route('course.edit', $course->getKey()))
                    ->setMessage('Cannot move start date forward. There are ' . $sessionsWithBookings . ' sessions before ' . $newStartDate . ' with existing bookings.')
                    ->toResponse($request);
            }

            $sessionsToDelete = $course->sessions()
                ->where('is_manual', false)
                ->where('start_date', '<', $newStartDate)
                ->doesntHave('bookings')
                ->get();

            foreach ($sessionsToDelete as $session) {
                $session->delete();
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

        $this->generateSessions($duplicatedCourse);

        return $this->httpResponse()
            ->setPreviousRoute('course.edit', $course->getKey())
            ->setNextRoute('course.edit', $duplicatedCourse->getKey())
            ->setMessage(trans('core/acl::permissions.duplicated_success'));
    }

    protected function generateSessions(Course $course): void
    {
        $dates = [];

        $durationSeconds = 3600;
        if ($course->start_date && $course->end_date) {
            $durationSeconds = abs($course->end_date->diffInSeconds($course->start_date));
            if ($durationSeconds <= 0) $durationSeconds = 3600;
        }

        if ($course->isRecurring()) {
            $recurringDates = $course->generateRecurringDates();

            foreach ($recurringDates as $startCarbon) {
                if (!($startCarbon instanceof \Carbon\Carbon)) continue;

                $endCarbon = $startCarbon->copy()->addSeconds($durationSeconds);

                if ($course->recurring_until && $endCarbon->gt($course->recurring_until)) {
                    continue;
                }

                $dates[] = [
                    'start_date' => $startCarbon->toDateTimeString(),
                    'end_date'   => $endCarbon->toDateTimeString(),
                    'is_manual'  => false,
                ];
            }
        }
        else {
            if ($course->start_date) {
                $dates[] = [
                    'start_date' => $course->start_date->toDateTimeString(),
                    'end_date'   => $course->end_date ? $course->end_date->toDateTimeString() : null,
                    'is_manual'  => false,
                ];
            }
        }

        $manualSessionsRaw = request()->input('manual_sessions', []);
        if (is_string($manualSessionsRaw)) {
            $manualSessionsRaw = json_decode($manualSessionsRaw, true) ?: [];
        }

        $manualSessionsFromForm = [];
        foreach ($manualSessionsRaw as $row) {
            if (!is_array($row)) continue;
            $sessionData = [];
            foreach ($row as $field) {
                if (!is_array($field)) continue;
                $key = $field['key'] ?? null;
                $value = $field['value'] ?? null;
                if ($key) $sessionData[$key] = $value;
            }

            if (!empty($sessionData['start']) && !empty($sessionData['end'])) {
                try {
                    $manualSessionsFromForm[] = [
                        'id'         => $sessionData['id'] ?? null,
                        'start_date' => \Carbon\Carbon::parse($sessionData['start'])->toDateTimeString(),
                        'end_date'   => \Carbon\Carbon::parse($sessionData['end'])->toDateTimeString(),
                        'is_manual'  => true,
                    ];
                } catch (\Exception $e) {
                    continue;
                }
            }
        }

        $unique = [];
        $dates = array_filter($dates, function ($d) use (&$unique) {
            if (!isset($d['start_date']) || isset($unique[$d['start_date']])) return false;
            $unique[$d['start_date']] = true;
            return true;
        });

        $existingManuals = $course->sessions()->where('is_manual', true)->with('bookings')->get();
        $submittedIds = collect($manualSessionsFromForm)->pluck('id')->filter()->toArray();

        foreach ($manualSessionsFromForm as $data) {
            if (!empty($data['id'])) {
                $session = $existingManuals->firstWhere('id', (int)$data['id']);
                if ($session) {
                    $session->update([
                        'start_date' => $data['start_date'],
                        'end_date'   => $data['end_date'],
                    ]);
                    continue;
                }
            }

            $course->sessions()->create([
                'start_date' => $data['start_date'],
                'end_date'   => $data['end_date'],
                'is_manual'  => true,
                'in_recurrence' => false,
                'available_seats' => $course->unlimited_seats ? null : $course->number_of_seats,
            ]);
        }

        $removedSessions = $existingManuals->filter(fn($s) => !in_array($s->id, $submittedIds));
        foreach ($removedSessions as $session) {
            if ($session->activeBookings()->exists()) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'manual_sessions' => __("Cannot remove session starting at :date; it has bookings.", [
                        'date' => $session->start_date->format('Y-m-d H:i'),
                    ]),
                ]);
            }
            $session->delete();
        }

        foreach ($dates as $d) {
            $course->sessions()->updateOrCreate(
                [
                    'start_date' => $d['start_date'],
                    'end_date'   => $d['end_date'],
                ],
                [
                    'available_seats' => $course->unlimited_seats ? null : $course->number_of_seats,
                    'in_recurrence' => !$d['is_manual'],
                    'is_manual' => $d['is_manual'],
                ]
            );
        }
    }

    public function list(int $courseId): JsonResponse
    {
        $sessions = CourseSession::query()
            ->where('course_id', $courseId)
            ->select('id', 'start_date', 'end_date', 'available_seats')
            ->orderBy('start_date')
            ->get();

        $grouped = $sessions->groupBy(fn ($session) =>
        Carbon::parse($session->start_date)->format('Y-m-d')
        );

        $formatted = collect();

        foreach ($grouped as $date => $items) {
            foreach ($items as $session) {
                $start = Carbon::parse($session->start_date);
                $end = Carbon::parse($session->end_date);

                $text = $start->format('d/m/Y h:i A') . ' - ' . $end->format('h:i A');

                $formatted->push([
                    'id' => $session->id,
                    'text' => $text,
                ]);
            }
        }

        return response()->json(['data' => $formatted->values()]);
    }

}
