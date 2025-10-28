<?php

namespace Botble\Courses\Tables;

use Botble\Base\Facades\Assets;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Models\MetaBox;
use Botble\Courses\Models\Course;
use Botble\Courses\Models\CourseBooking;
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
                    ->orderable(true)
                    ->name('occupancy_value')
                    ->searchable(false)
                    ->escape(false)
                    ->getValueUsing(fn (FormattedColumn $column) => $this->renderOccupancy($column->getItem())),

                FormattedColumn::make('engagement')
                    ->title(trans('plugins/courses::courses.table.engagement'))
                    ->orderable(true)
                    ->name('engagement_value')
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
                    ->orderable(true)
                    ->name('course_sessions.start_date')
                    ->searchable(false)
                    ->escape(false)
                    ->getValueUsing(fn (FormattedColumn $column) => $this->renderSchedule($column->getItem())),

                FormattedColumn::make('score')
                    ->title(trans('plugins/courses::courses.table.score'))
                    ->orderable(true)
                    ->name('score_value')
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
                $activeStatuses = [
                    BookingStatusEnum::PENDING,
                    BookingStatusEnum::PROCESSING,
                    BookingStatusEnum::COMPLETED,
                ];

                $statusList = implode("','", $activeStatuses);

                $bookingStats = CourseBooking::query()
                    ->selectRaw('course_session_id')
                    ->selectRaw("SUM(CASE WHEN status IN ('{$statusList}') THEN 1 ELSE 0 END) as active_bookings")
                    ->selectRaw("SUM(CASE WHEN status = '" . BookingStatusEnum::COMPLETED . "' THEN 1 ELSE 0 END) as attended_bookings")
                    ->groupBy('course_session_id');

                $viewSub = MetaBox::query()
                    ->selectRaw('reference_id')
                    ->selectRaw('MAX(CAST(meta_value AS UNSIGNED)) as views')
                    ->where('meta_key', 'views')
                    ->where('reference_type', Course::class)
                    ->groupBy('reference_id');

                $maxViewsReference = max(1, $this->performance()->maxReferenceViews());

                $viewsExpr = 'COALESCE(course_views.views, 0)';
                $activeExpr = 'COALESCE(booking_stats.active_bookings, 0)';
                $attendedExpr = 'COALESCE(booking_stats.attended_bookings, 0)';
                $availableSeatsExpr = 'course_sessions.available_seats';

                $occupancyExpr = "CASE WHEN $availableSeatsExpr IS NULL THEN CASE WHEN $activeExpr > 0 THEN 100 ELSE 0 END ELSE LEAST((($activeExpr / NULLIF($availableSeatsExpr, 0)) * 100), 100) END";
                $engagementExpr = "CASE WHEN $activeExpr > 0 THEN LEAST((($attendedExpr / NULLIF($activeExpr, 0)) * 100), 100) ELSE 0 END";
                $conversionExpr = "CASE WHEN $viewsExpr > 0 THEN LEAST(((($activeExpr / NULLIF($viewsExpr, 0)) * 100) * 10), 100) ELSE 0 END";
                $viewScoreExpr = "LEAST((($viewsExpr / {$maxViewsReference}) * 100), 100)";
                $scoreExpr = "ROUND(LEAST((($viewScoreExpr * 0.25) + ($occupancyExpr * 0.35) + ($engagementExpr * 0.25) + ($conversionExpr * 0.15)), 100), 1)";

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
                    ->leftJoin('courses', 'courses.id', '=', 'course_sessions.course_id')
                    ->leftJoinSub($bookingStats, 'booking_stats', 'booking_stats.course_session_id', '=', 'course_sessions.id')
                    ->leftJoinSub($viewSub, 'course_views', 'course_views.reference_id', '=', 'course_sessions.course_id')
                    ->select([
                        'course_sessions.id',
                        'course_sessions.course_id',
                        'course_sessions.start_date',
                        'course_sessions.end_date',
                        'course_sessions.available_seats',
                        'course_sessions.created_at',
                    ])
                    ->selectRaw('courses.name as session_overview')
                    ->selectRaw('courses.price as price')
                    ->selectRaw("$viewsExpr as views")
                    ->selectRaw("$occupancyExpr as occupancy")
                    ->selectRaw("$engagementExpr as engagement")
                    ->selectRaw("$activeExpr as participants")
                    ->selectRaw('course_sessions.start_date as schedule')
                    ->selectRaw("$scoreExpr as score")
                    ->selectRaw("$occupancyExpr as occupancy_value")
                    ->selectRaw("$engagementExpr as engagement_value")
                    ->selectRaw("$scoreExpr as score_value")
                    ->selectRaw("$activeExpr as active_bookings_count")
                    ->selectRaw("$attendedExpr as attended_bookings_count");
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

        $metaHtml = $metaParts
            ? '<div class="text-muted small text-truncate">' . implode(' • ', $metaParts) . '</div>'
            : '';

        return <<<HTML
<div class="d-flex align-items-center gap-3">
    <div class="flex-shrink-0 rounded-3 overflow-hidden" style="width:60px;height:60px;background:#F4F7F6;">
        <img src="{$thumbnail}" alt="{$courseName}" style="width:100%;height:100%;object-fit:cover;">
    </div>
    <div class="flex-grow-1">
        <a href="{$courseUrl}" class="fw-semibold text-decoration-none" style="color:#1F2A2A;">{$courseName}</a>
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

        return '<span class="fw-semibold" style="color:#1F2A2A;">' . format_price($course->price) . '</span>';
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

        $reference = max(1, $this->performance()->maxReferenceViews());
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

        $segments = 8;
        $filledSegments = (int) round(min($views / $reference, 1) * $segments);
        $filledSegments = max(0, min($segments, $filledSegments));

        $segmentsMarkup = '';

        for ($i = 0; $i < $segments; $i++) {
            $isFilled = $i < $filledSegments;
            $color = $isFilled ? '#2F6A62' : '#DDE6E4';
            $segmentsMarkup .= '<span style="display:inline-block;width:12px;height:6px;border-radius:4px;background:' . $color . ';margin-right:6px;"></span>';
        }

        $viewsBadge = '<span class="px-3 py-1 rounded-pill" style="background:#F4F9F8;color:#24554F;font-weight:600;">' . number_format($views) . '</span>';

        return <<<HTML
<div class="d-flex flex-column gap-2" title="{$title}">
    <div class="d-flex align-items-center gap-3">
        {$viewsBadge}
        <div class="d-flex align-items-center">{$segmentsMarkup}</div>
    </div>
    <div class="text-muted small">{$hintHtml}</div>
</div>
HTML;
    }

    protected function renderOccupancy(CourseSession $session): string
    {
        $stats = $this->performance()->forSession($session);
        $booked = (int) $stats['booked_seats'];
        $maxSeats = $stats['max_seats'];

        if ($maxSeats !== null) {
            $label = trans('plugins/courses::courses.table.booked_vs_remaining', [
                'booked' => $booked,
                'remaining' => $stats['remaining_seats'],
            ]);
            $capacity = max($maxSeats, 1);
            $ratio = $capacity > 0 ? $booked / $capacity : 0;
        } else {
            $label = trans('plugins/courses::courses.table.booked_unlimited', [
                'booked' => $booked,
            ]);
            $ratio = $booked > 0 ? 1 : 0;
        }

        $chairs = 5;
        $filledChairs = (int) round(min(max($ratio, 0), 1) * $chairs);
        $filledChairs = max(0, min($chairs, $filledChairs));

        $icons = '';

        for ($i = 0; $i < $chairs; $i++) {
            $isFilled = $i < $filledChairs;
            $color = $isFilled ? '#2F6A62' : '#E0EAE7';
            $icons .= '<span style="display:inline-block;width:18px;height:18px;border-radius:6px;background:' . $color . ';margin-right:6px;"></span>';
        }

        return <<<HTML
<div class="d-flex flex-column gap-2">
    <div class="d-flex align-items-center gap-3">
        <span class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width:34px;height:34px;background:#E6F1EF;color:#30655F;font-weight:600;">{$booked}</span>
        <div class="d-inline-flex align-items-center">{$icons}</div>
    </div>
    <div class="text-muted small">{$label}</div>
</div>
HTML;
    }

    protected function renderEngagement(CourseSession $session): string
    {
        $stats = $this->performance()->forSession($session);
        $engagement = number_format($stats['engagement_percent'], 1);
        $conversion = number_format($stats['conversion_percent'], 1);
        $views = number_format($stats['views']);

        $engagementLabel = trans('plugins/courses::courses.table.engagement_ratio', [
            'attended' => $stats['engaged_participants'],
            'booked' => $stats['booked_seats'],
        ]);

        $conversionLabel = trans('plugins/courses::courses.table.conversion_rate', [
            'percent' => $conversion,
        ]);

        $viewsLabel = trans('plugins/courses::courses.table.engagement_views', [
            'count' => $views,
        ]);

        return <<<HTML
<div class="d-flex flex-column gap-2">
    <div class="d-inline-flex align-items-center gap-2">
        <span class="px-3 py-2 rounded-pill" style="background:#EEF5F4;color:#24554F;font-weight:600;">{$engagement}%</span>
        <span class="text-muted small">{$viewsLabel}</span>
    </div>
    <div class="text-muted small">{$engagementLabel}</div>
    <div class="text-muted small">{$conversionLabel}</div>
</div>
HTML;
    }

    protected function renderParticipants(CourseSession $session): string
    {
        $buttonLabel = e(trans('plugins/courses::courses.table.view_participants'));
        $sessionId = $session->getKey();

        return <<<HTML
<button type="button" class="btn btn-sm view-participants-btn d-inline-flex align-items-center gap-2" data-session-id="{$sessionId}" style="background:#F4F9F8;color:#24554F;border:1px solid #C7DBD7;">
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

        $timeBadge = $timeRange !== '—'
            ? '<span class="px-3 py-1 rounded-pill" style="background:#F4F9F8;color:#24554F;font-weight:500;">' . $timeRange . '</span>'
            : '<span class="text-muted small">' . $timeRange . '</span>';

        return <<<HTML
<div class="fw-semibold" style="color:#1F2A2A;">{$startDate}</div>
<div>{$timeBadge}</div>
HTML;
    }

    protected function renderScore(CourseSession $session): string
    {
        $stats = $this->performance()->forSession($session);
        $score = number_format($stats['score'], 1);

        [$background, $textColor, $ratingKey] = $this->resolveScoreStyle((float) $stats['score']);
        $ratingLabel = trans($ratingKey);
        $title = e($ratingLabel);

        return <<<HTML
<div class="d-flex align-items-center gap-3">
    <span class="px-4 py-2 rounded-pill" title="{$title}" style="background:{$background};color:{$textColor};font-weight:700;min-width:88px;text-align:center;">{$score}</span>
    <div class="text-muted small">{$ratingLabel}</div>
</div>
HTML;
    }

    protected function resolveScoreStyle(float $score): array
    {
        return match (true) {
            $score >= 85 => ['#2F6A62', '#FFFFFF', 'plugins/courses::courses.table.score_rating.great'],
            $score >= 65 => ['#4E8E85', '#FFFFFF', 'plugins/courses::courses.table.score_rating.good'],
            $score >= 45 => ['#F2B138', '#2B2B2B', 'plugins/courses::courses.table.score_rating.fair'],
            default => ['#D96C5F', '#FFFFFF', 'plugins/courses::courses.table.score_rating.poor'],
        };
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
