<?php

namespace Botble\Courses\Http\Controllers;

use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\Courses\Http\Requests\CourseRequest;
use Botble\Courses\Models\Course;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Courses\Tables\CourseTable;
use Botble\Courses\Forms\CourseForm;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;

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
        // 1) Build base $dates array depending on recurring flag
        $dates = [];

        if ($course->isRecurring()) {
            // Course is recurring -> use generateRecurringDates() only (do not add single main session again)
            $recurring = $course->generateRecurringDates(); // array of Carbon or arrays

            foreach ($recurring as $r) {
                if ($r instanceof Carbon) {
                    $startCarbon = $r->copy();
                    // If course->end_date has a time, apply that time to this date
                    if ($course->end_date) {
                        $endCarbon = $r->copy()->setTimeFrom($course->end_date);
                    } else {
                        // fallback: same as start + 1 hour
                        $endCarbon = $startCarbon->copy()->addHour();
                    }

                    $dates[] = [
                        'start_date' => $startCarbon->toDateTimeString(),
                        'end_date'   => $endCarbon->toDateTimeString(),
                        'is_manual'  => false,
                    ];
                } elseif (is_array($r)) {
                    // already an array (expected format)
                    $dates[] = $r;
                }
            }
        } else {
            // Not recurring => include the course main start/end as single session (if start exists)
            if ($course->start_date) {
                $dates[] = [
                    'start_date' => $course->start_date->toDateTimeString(),
                    'end_date'   => $course->end_date ? $course->end_date->toDateTimeString() : null,
                    'is_manual'  => false,
                ];
            }
        }

        // 2) Parse manual sessions from the RepeaterField reliably (with ID)
        $manualSessionsRaw = request()->input('manual_sessions', []);
        if (is_string($manualSessionsRaw)) {
            $manualSessionsRaw = json_decode($manualSessionsRaw, true) ?: [];
        }

        $manualSessionsFromForm = [];

        foreach ($manualSessionsRaw as $row) {
            // expecting $row = [ ['key'=>'id','value'=>'...'], ['key'=>'start','value'=>'...'], ['key'=>'end','value'=>'...'] ]
            if (!is_array($row)) {
                continue;
            }

            $sessionData = [];
            foreach ($row as $field) {
                if (!is_array($field)) {
                    continue;
                }
                $key = $field['key'] ?? null;
                $value = $field['value'] ?? null;
                if ($key) {
                    $sessionData[$key] = $value;
                }
            }

            if (!empty($sessionData['start']) && !empty($sessionData['end'])) {
                try {
                    $start = Carbon::parse($sessionData['start'])->toDateTimeString();
                    $end   = Carbon::parse($sessionData['end'])->toDateTimeString();
                } catch (\Exception $e) {
                    continue;
                }

                $manualSessionsFromForm[] = [
                    'id'         => $sessionData['id'] ?? null,
                    'start_date' => $start,
                    'end_date'   => $end,
                    'is_manual'  => true,
                ];
            }
        }


        // 3) Remove duplicated start_date entries (keep first occurrence)
        $unique = [];
        $cleanDates = [];

        foreach ($dates as $d) {
            // ensure we have string start_date
            $startKey = is_array($d) && isset($d['start_date']) ? (string) $d['start_date'] : null;
            if (! $startKey) {
                continue;
            }
            if (isset($unique[$startKey])) {
                // duplicate start -> skip. If you prefer manual to override recurrence, adjust logic here.
                continue;
            }
            $unique[$startKey] = true;
            $cleanDates[] = $d;
        }

        $dates = $cleanDates;

        // 4) Sync with existing sessions: delete sessions not present in $dates (except those with bookings)
        // 4) Sync manual sessions (update / delete with booking protection)
        $existingManuals = $course->sessions()->where('is_manual', true)->with('bookings')->get();
        $submittedIds = collect($manualSessionsFromForm)->pluck('id')->filter()->toArray();

// 🔹 Update or create manual sessions
        foreach ($manualSessionsFromForm as $data) {
            if (!empty($data['id'])) {
                $session = $existingManuals->firstWhere('id', (int)$data['id']);
                if ($session) {
                    // update if changed
                    $session->update([
                        'start_date' => $data['start_date'],
                        'end_date'   => $data['end_date'],
                    ]);
                    continue;
                }
            }

            // new manual session
            $course->sessions()->create([
                'start_date' => $data['start_date'],
                'end_date'   => $data['end_date'],
                'is_manual'  => true,
                'in_recurrence' => false,
                'available_seats' => $course->unlimited_seats ? null : $course->number_of_seats,
            ]);
        }

// 🔹 Detect and delete removed sessions
        $removedSessions = $existingManuals->filter(fn($s) => !in_array($s->id, $submittedIds));

        foreach ($removedSessions as $session) {
            if ($session->bookings()->exists()) {
                throw ValidationException::withMessages([
                    'manual_sessions' => __("You cannot remove the session starting at :date because it has existing bookings.", [
                        'date' => $session->start_date->format('Y-m-d H:i'),
                    ]),
                ]);
            }

            $session->delete();
        }


        // 5) Create missing sessions (using normalized datetimes)
        foreach ($dates as $d) {
            // parse to Carbon and format to DB string to avoid mismatches
            try {
                $start = Carbon::parse($d['start_date'])->toDateTimeString();
                $end = $d['end_date'] ? Carbon::parse($d['end_date'])->toDateTimeString() : null;
            } catch (\Exception $e) {
                // skip invalid
                continue;
            }

            $course->sessions()->firstOrCreate(
                [
                    'start_date' => $start,
                    'end_date'   => $end,
                ],
                [
                    'available_seats' => $course->unlimited_seats ? null : $course->number_of_seats,
                    'in_recurrence'   => !($d['is_manual'] ?? false),
                    'is_manual'       => $d['is_manual'] ?? false,
                ]
            );
        }
    }


    public function list(int $courseId): JsonResponse
    {
        $sessions = \Botble\Courses\Models\CourseSession::query()
            ->where('course_id', $courseId)
            ->select('id', 'start_date', 'end_date')
            ->orderBy('start_date')
            ->get()->filter(fn ($session) => $session->hasAvailableSeats());

        // Group sessions by the same date (Y-m-d)
        $grouped = $sessions->groupBy(function ($session) {
            return Carbon::parse($session->start_date)->format('Y-m-d');
        });

        $formatted = collect();

        foreach ($grouped as $date => $items) {
            foreach ($items as $session) {
                $start = Carbon::parse($session->start_date);
                $end = Carbon::parse($session->end_date);

                // Format time like 29/10/2024 11:00 AM - 01:00 PM
                $text = $start->format('d/m/Y h:i A') . ' - ' . $end->format('h:i A');

                $formatted->push([
                    'id' => $session->id,
                    'text' => $text,
                ]);
            }
        }

        return response()->json(['data' => $formatted]);
    }
}
