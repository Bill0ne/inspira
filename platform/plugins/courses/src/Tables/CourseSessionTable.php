<?php

namespace Botble\Courses\Tables;

use Botble\Base\Facades\Assets;
use Botble\Base\Facades\BaseHelper;
use Botble\Courses\Models\CourseSession;
use Botble\Courses\Services\CoursePerformanceService;
use Botble\Hotel\Enums\BookingStatusEnum;
use Botble\Media\Facades\RvMedia;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\BulkActions\DeleteBulkAction;
use Botble\Table\BulkChanges\CreatedAtBulkChange;
use Botble\Table\BulkChanges\DateBulkChange;
use Botble\Table\BulkChanges\SelectBulkChange;
use Botble\Table\Columns\FormattedColumn;
use Botble\Table\Columns\IdColumn;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Relation as EloquentRelation;
use Illuminate\Database\Query\Builder as QueryBuilder;

class CourseSessionTable extends TableAbstract
{
    protected ?CoursePerformanceService $performanceService = null;

    public function setup(): void
    {
        Assets::addScriptsDirectly(['vendor/core/plugins/courses/js/script.js']);

        $this
            ->model(CourseSession::class)
            ->addColumns([
                IdColumn::make(),

                FormattedColumn::make('session_overview')
                    ->title(trans('plugins/courses::courses.table.overview'))
                    ->orderable(false)
                    ->searchable(false)
                    ->escape(false)
                    ->getValueUsing(fn (FormattedColumn $column) => $this->renderSessionOverview($column->getItem())),

                FormattedColumn::make('price')
                    ->title(trans('plugins/courses::courses.course.price'))
                    ->orderable(false)
                    ->searchable(false)
                    ->getValueUsing(fn (FormattedColumn $column) => $this->renderPrice($column->getItem())),

                FormattedColumn::make('views')
                    ->title(trans('plugins/courses::courses.table.views'))
                    ->orderable(false)
                    ->searchable(false)
                    ->escape(false)
                    ->getValueUsing(fn (FormattedColumn $column) => $this->renderViews($column->getItem())),

                FormattedColumn::make('occupancy')
                    ->title(trans('plugins/courses::courses.table.occupancy'))
                    ->orderable(false)
                    ->searchable(false)
                    ->escape(false)
                    ->getValueUsing(fn (FormattedColumn $column) => $this->renderOccupancy($column->getItem())),

                FormattedColumn::make('engagement')
                    ->title(trans('plugins/courses::courses.table.engagement'))
                    ->orderable(false)
                    ->searchable(false)
                    ->escape(false)
                    ->getValueUsing(fn (FormattedColumn $column) => $this->renderEngagement($column->getItem())),

                FormattedColumn::make('schedule')
                    ->title(trans('plugins/courses::courses.table.schedule'))
                    ->orderable(false)
                    ->searchable(false)
                    ->escape(false)
                    ->getValueUsing(fn (FormattedColumn $column) => $this->renderSchedule($column->getItem())),

                FormattedColumn::make('score')
                    ->title(trans('plugins/courses::courses.table.score'))
                    ->orderable(false)
                    ->searchable(false)
                    ->escape(false)
                    ->getValueUsing(fn (FormattedColumn $column) => $this->renderScore($column->getItem())),
            ])
            ->addBulkActions([
                DeleteBulkAction::make()->permission('course-sessions.destroy'),
            ])
            ->addBulkChanges([
                SelectBulkChange::make()
                    ->name('course_id')
                    ->title(trans('plugins/courses::courses.course.name'))
                    ->choices(\Botble\Courses\Models\Course::query()->pluck('name', 'id')->all()),
                DateBulkChange::make()
                    ->name('start_date')
                    ->title(trans('plugins/courses::courses.course.start_date')),
                DateBulkChange::make()
                    ->name('end_date')
                    ->title(trans('plugins/courses::courses.course.end_date')),
                CreatedAtBulkChange::make(),
            ])
            ->queryUsing(function (Builder $query) {
                return $query
                    ->with([
                        'course' => function (BelongsTo $query) {
                            $query->select([
                                'id',
                                'name',
                                'thumbnail',
                                'price',
                                'category_id',
                                'instructor_id',
                                'status',
                                'unlimited_seats',
                                'number_of_seats',
                            ])
                                ->with(['category:id,name', 'instructor:id,name']);
                        },
                    ])
                    ->withCount([
                        'bookings as active_bookings_count' => function ($query) {
                            $query->whereIn('status', [
                                BookingStatusEnum::PENDING,
                                BookingStatusEnum::PROCESSING,
                                BookingStatusEnum::COMPLETED,
                            ]);
                        },
                        'bookings as attended_bookings_count' => function ($query) {
                            $query->where('status', BookingStatusEnum::COMPLETED);
                        },
                    ])
                    ->select([
                        'id',
                        'course_id',
                        'start_date',
                        'end_date',
                        'available_seats',
                        'created_at',
                    ]);
            })
            ->onFilterQuery(function (
                EloquentBuilder|QueryBuilder|EloquentRelation $query,
                string $key,
                string $operator,
                ?string $value
            ) {
                if (! $value) {
                    return false;
                }

                if (in_array($key, ['start_date', 'end_date'])) {
                    try {
                        $startOfDay = \Carbon\Carbon::parse($value)->startOfDay();
                        $endOfDay = \Carbon\Carbon::parse($value)->endOfDay();
                    } catch (\Exception $e) {
                        return false;
                    }

                    if ($key === 'start_date') {
                        return $query->whereBetween('start_date', [$startOfDay, $endOfDay]);
                    }

                    if ($key === 'end_date') {
                        return $query->whereBetween('end_date', [$startOfDay, $endOfDay]);
                    }
                }

                if ($key === 'course_id') {
                    return $query->where('course_id', $value);
                }

                return false;
            });
    }

    public function hasOperations(): bool
    {
        return false;
    }

    protected function renderSessionOverview(CourseSession $session): string
    {
        $course = $session->course;

        if (! $course) {
            return '<span class="text-muted">—</span>';
        }

        $thumbnail = RvMedia::getImageUrl(
            $course->thumbnail,
            'thumb',
            false,
            RvMedia::getDefaultImage()
        );

        $courseName = BaseHelper::clean($course->name ?? '—');
        $courseUrl = route('course.edit', $course->getKey());

        $badges = [];

        if ($course->category?->name) {
            $badges[] = sprintf(
                '<span class="badge bg-light text-body border rounded-pill px-3 py-1">%s</span>',
                BaseHelper::clean($course->category->name)
            );
        }

        if ($course->instructor?->name) {
            $badges[] = sprintf(
                '<span class="badge bg-light text-body border rounded-pill px-3 py-1">%s</span>',
                BaseHelper::clean($course->instructor->name)
            );
        }

        $badgesHtml = implode(' ', $badges);

        $statusBadge = $course->status?->toHtml() ?? '';

        $participantsLabel = trans('plugins/courses::courses.table.view_participants');

        $sessionLabel = trans('plugins/courses::courses.table.session_id', ['id' => $session->getKey()]);

        return <<<HTML
<div class="d-flex align-items-center gap-3">
    <div class="flex-shrink-0">
        <img src="{$thumbnail}" alt="{$courseName}" class="rounded" style="width:56px;height:56px;object-fit:cover;">
    </div>
    <div class="flex-grow-1">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{$courseUrl}" class="fw-semibold text-decoration-none text-body">{$courseName}</a>
            {$statusBadge}
        </div>
        <div class="text-muted small mt-1">{$sessionLabel}</div>
        <div class="d-flex align-items-center gap-2 flex-wrap mt-2">
            {$badgesHtml}
        </div>
        <div class="mt-2">
            <button class="btn btn-outline-primary btn-sm view-participants-btn" data-session-id="{$session->getKey()}">
                {$participantsLabel}
            </button>
        </div>
    </div>
</div>
HTML;
    }

    protected function renderPrice(CourseSession $session): string
    {
        $course = $session->course;

        if (! $course || $course->price === null) {
            return '—';
        }

        return format_price($course->price);
    }

    protected function renderViews(CourseSession $session): string
    {
        $stats = $this->performance()->forSession($session);
        $views = number_format($stats['views']);
        $hint = trans('plugins/courses::courses.table.views_hint');

        return sprintf('<div class="fw-semibold">%s</div><div class="text-muted small">%s</div>', $views, $hint);
    }

    protected function renderOccupancy(CourseSession $session): string
    {
        $stats = $this->performance()->forSession($session);
        $percent = (int) round(min($stats['occupancy_percent'], 100));
        $booked = $stats['booked_seats'];

        if ($stats['max_seats'] !== null) {
            $label = trans('plugins/courses::courses.table.booked_vs_remaining', [
                'booked' => $booked,
                'remaining' => $stats['remaining_seats'],
            ]);
        } else {
            $label = trans('plugins/courses::courses.table.booked_unlimited', [
                'booked' => $booked,
            ]);
        }

        return <<<HTML
<div class="small text-muted">{$label}</div>
<div style="height:6px;background:#e9ecef;border-radius:999px;overflow:hidden;margin-top:6px;">
    <div style="width:{$percent}%;background:#578E88;height:6px;"></div>
</div>
HTML;
    }

    protected function renderEngagement(CourseSession $session): string
    {
        $stats = $this->performance()->forSession($session);
        $engagement = number_format($stats['engagement_percent'], 1);
        $conversion = number_format($stats['conversion_percent'], 1);

        $engagementLabel = trans('plugins/courses::courses.table.engagement_ratio', [
            'attended' => $stats['engaged_participants'],
            'booked' => $stats['booked_seats'],
        ]);

        $conversionLabel = trans('plugins/courses::courses.table.conversion_rate', [
            'percent' => $conversion,
        ]);

        return <<<HTML
<div class="fw-semibold">{$engagement}%</div>
<div class="text-muted small">{$engagementLabel}</div>
<div class="text-muted small">{$conversionLabel}</div>
HTML;
    }

    protected function renderSchedule(CourseSession $session): string
    {
        $start = $this->formatDate($session->start_date, 'd.m.Y H:i');
        $end = $this->formatDate($session->end_date, 'd.m.Y H:i');

        return <<<HTML
<div class="fw-semibold">{$start}</div>
<div class="text-muted small">{$end}</div>
HTML;
    }

    protected function renderScore(CourseSession $session): string
    {
        $stats = $this->performance()->forSession($session);
        $score = $stats['score'];
        $percentage = max(0, min(100, (int) round($score)));

        $scoreHint = trans('plugins/courses::courses.table.score_hint');

        return <<<HTML
<div class="d-flex flex-column align-items-center gap-2">
    <div style="width:52px;height:52px;border-radius:50%;background:conic-gradient(#578E88 {$percentage}%, #e9ecef {$percentage}%);display:flex;align-items:center;justify-content:center;font-weight:600;color:#2b2b2b;">
        {$score}
    </div>
    <span class="text-muted small">{$scoreHint}</span>
</div>
HTML;
    }

    protected function performance(): CoursePerformanceService
    {
        return $this->performanceService ??= app(CoursePerformanceService::class);
    }

    protected function formatDate(?CarbonInterface $date, string $format): string
    {
        return $date ? $date->format($format) : '—';
    }
}
