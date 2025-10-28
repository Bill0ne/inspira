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

                FormattedColumn::make('participants')
                    ->title(trans('plugins/courses::courses.table.participants'))
                    ->orderable(false)
                    ->searchable(false)
                    ->escape(false)
                    ->getValueUsing(fn (FormattedColumn $column) => $this->renderParticipants($column->getItem())),

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

        $metaParts = array_filter([
            $course->category?->name ? BaseHelper::clean($course->category->name) : null,
            $course->instructor?->name ? BaseHelper::clean($course->instructor->name) : null,
        ]);

        $metaLine = $metaParts ? implode(' • ', $metaParts) : null;
        $sessionLabel = trans('plugins/courses::courses.table.session_id', ['id' => $session->getKey()]);

        $statusBadge = $course->status?->toHtml() ?? '';

        $metaHtml = array_filter([$sessionLabel, $metaLine]);
        $metaHtml = $metaHtml
            ? '<div class="text-muted small text-truncate">' . implode(' • ', $metaHtml) . '</div>'
            : '';

        return <<<HTML
<div class="d-flex align-items-center gap-3">
    <div class="flex-shrink-0 rounded overflow-hidden" style="width:56px;height:56px;">
        <img src="{$thumbnail}" alt="{$courseName}" style="width:100%;height:100%;object-fit:cover;">
    </div>
    <div class="flex-grow-1">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{$courseUrl}" class="fw-semibold text-decoration-none text-body">{$courseName}</a>
            {$statusBadge}
        </div>
        {$metaHtml}
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
        $viewsSource = $stats['views_source'] ?? 'none';
        $views = (int) ($stats['views'] ?? 0);

        if ($viewsSource === 'none') {
            $hint = trans('plugins/courses::courses.table.views_hint_unavailable');

            return '<div class="text-muted small">' . BaseHelper::clean($hint) . '</div>';
        }

        $maxStars = 5;
        $reference = max(1, $this->performance()->maxReferenceViews());
        $filledStars = (int) round(min($views / $reference, 1) * $maxStars);
        $filledStars = max(0, min($maxStars, $filledStars));

        $starsMarkup = str_repeat('★', $filledStars) . str_repeat('☆', $maxStars - $filledStars);
        $viewsLabel = trans('plugins/courses::courses.table.views_rating_label', [
            'count' => number_format($views),
        ]);

        $hintKey = match ($viewsSource) {
            'analytics' => 'plugins/courses::courses.table.views_hint',
            'metadata' => 'plugins/courses::courses.table.views_hint_fallback',
            default => 'plugins/courses::courses.table.views_hint_unavailable',
        };

        $hintParams = [];

        if ($viewsSource === 'analytics') {
            $hintParams['days'] = $stats['analytics_days'] ?? $this->performance()->analyticsDays();
        }

        $hint = trans($hintKey, $hintParams);
        $hintHtml = e($hint);
        $title = e($viewsLabel);

        return <<<HTML
<div class="d-flex flex-column align-items-start">
    <span class="fs-5" style="letter-spacing:2px;color:#578E88;" title="{$title}">{$starsMarkup}</span>
    <span class="text-muted small">{$hintHtml}</span>
</div>
HTML;
    }

    protected function renderOccupancy(CourseSession $session): string
    {
        $stats = $this->performance()->forSession($session);
        $percent = (int) round(min($stats['occupancy_percent'], 100));
        $booked = (int) $stats['booked_seats'];

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

        $segments = 10;
        $filledSegments = (int) round(($percent / 100) * $segments);
        $filledSegments = max(0, min($segments, $filledSegments));

        $segmentsHtml = '';

        for ($i = 0; $i < $segments; $i++) {
            $color = $i < $filledSegments ? '#578E88' : '#E6EAE9';
            $segmentsHtml .= '<span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:'
                . $color . ';margin-right:4px;"></span>';
        }

        return <<<HTML
<div class="d-flex align-items-center gap-3">
    <span class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width:28px;height:28px;background:#E6F1EF;color:#30655F;font-weight:600;">{$booked}</span>
    <span>{$segmentsHtml}</span>
</div>
<div class="text-muted small mt-2">{$label}</div>
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
<div class="d-flex flex-column align-items-start gap-1">
    <span class="badge rounded-pill px-3 py-2" style="background:#F2F5F4;color:#2B2B2B;font-weight:600;">{$engagement}%</span>
    <span class="text-muted small">{$engagementLabel}</span>
    <span class="text-muted small">{$conversionLabel}</span>
</div>
HTML;
    }

    protected function renderParticipants(CourseSession $session): string
    {
        $buttonLabel = e(trans('plugins/courses::courses.table.view_participants'));
        $sessionId = $session->getKey();

        return <<<HTML
<button type="button" class="btn btn-sm btn-outline-primary view-participants-btn d-inline-flex align-items-center gap-2" data-session-id="{$sessionId}">
    <i class="fa fa-users" aria-hidden="true"></i>
    <span>{$buttonLabel}</span>
</button>
HTML;
    }

    protected function renderSchedule(CourseSession $session): string
    {
        $startDate = $this->formatDate($session->start_date, 'd.m.Y');
        $timeRange = '—';

        if ($session->start_date && $session->end_date) {
            $timeRange = $session->start_date->format('H:i') . ' – ' . $session->end_date->format('H:i');
        } elseif ($session->start_date) {
            $timeRange = $session->start_date->format('H:i');
        }

        return <<<HTML
<div class="fw-semibold">{$startDate}</div>
<div class="text-muted small">{$timeRange}</div>
HTML;
    }

    protected function renderScore(CourseSession $session): string
    {
        $stats = $this->performance()->forSession($session);
        $score = number_format($stats['score'], 1);

        $scoreAutoLabel = trans('plugins/courses::courses.table.score_auto_label');

        return <<<HTML
<div class="d-flex flex-column align-items-start gap-1">
    <span class="badge rounded-pill px-3 py-2" style="background:#65AFA7;color:#ffffff;font-weight:600;min-width:96px;text-align:center;">{$score}</span>
    <span class="text-muted small">{$scoreAutoLabel}</span>
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
