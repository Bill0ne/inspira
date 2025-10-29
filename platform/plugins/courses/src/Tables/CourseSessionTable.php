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
    protected const STYLESHEET_FALLBACK_VERSION = '2024021501';

    protected ?CoursePerformanceService $performanceService = null;

    public function setup(): void
    {
        Assets::addScriptsDirectly(['vendor/core/plugins/courses/js/script.js']);
        Assets::addStylesDirectly([$this->versionedStylesheet()]);

        $this
            ->model(CourseSession::class)
            ->addColumns([
                IdColumn::make()
                    ->width(60)
                    ->alignStart()
                    ->getValueUsing(function (IdColumn $column) {
                        $id = (int) $column->getOriginalValue();

                        return <<<HTML
<span class="course-session-id badge rounded-pill">#{$id}</span>
HTML;
                    }),

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
                    ->alignStart()
                    ->getValueUsing(fn (FormattedColumn $column) => $this->renderPrice($column->getItem())),

                FormattedColumn::make('views')
                    ->title(trans('plugins/courses::courses.table.views'))
                    ->orderable(false)
                    ->searchable(false)
                    ->alignStart()
                    ->escape(false)
                    ->getValueUsing(fn (FormattedColumn $column) => $this->renderViews($column->getItem())),

                FormattedColumn::make('occupancy')
                    ->title(trans('plugins/courses::courses.table.occupancy'))
                    ->orderable(true)
                    ->name('occupancy_value')
                    ->searchable(false)
                    ->alignStart()
                    ->escape(false)
                    ->getValueUsing(fn (FormattedColumn $column) => $this->renderOccupancy($column->getItem())),

                FormattedColumn::make('participants')
                    ->title(trans('plugins/courses::courses.table.participants'))
                    ->orderable(false)
                    ->searchable(false)
                    ->alignCenter()
                    ->escape(false)
                    ->getValueUsing(fn (FormattedColumn $column) => $this->renderParticipants($column->getItem())),

                FormattedColumn::make('date')
                    ->title(trans('plugins/courses::courses.table.date'))
                    ->orderable(true)
                    ->name('course_sessions.start_date')
                    ->searchable(false)
                    ->alignStart()
                    ->escape(false)
                    ->getValueUsing(fn (FormattedColumn $column) => $this->renderDate($column->getItem())),

                FormattedColumn::make('time')
                    ->title(trans('plugins/courses::courses.table.time'))
                    ->orderable(true)
                    ->name('course_sessions.start_date')
                    ->searchable(false)
                    ->alignStart()
                    ->escape(false)
                    ->getValueUsing(fn (FormattedColumn $column) => $this->renderTime($column->getItem())),

                FormattedColumn::make('capacity')
                    ->title(trans('plugins/courses::courses.table.capacity'))
                    ->orderable(true)
                    ->name('course_sessions.available_seats')
                    ->searchable(false)
                    ->alignCenter()
                    ->escape(false)
                    ->getValueUsing(fn (FormattedColumn $column) => $this->renderCapacity($column->getItem())),

                FormattedColumn::make('created_at')
                    ->title(trans('plugins/courses::courses.table.created_at'))
                    ->orderable(true)
                    ->name('course_sessions.created_at')
                    ->searchable(false)
                    ->alignStart()
                    ->escape(false)
                    ->getValueUsing(fn (FormattedColumn $column) => $this->renderCreatedAt($column->getItem())),

                FormattedColumn::make('score')
                    ->title(trans('plugins/courses::courses.table.score'))
                    ->orderable(true)
                    ->name('score_value')
                    ->searchable(false)
                    ->alignEnd()
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
                    ->selectRaw("$activeExpr as participants")
                    ->selectRaw('course_sessions.start_date as start_date_value')
                    ->selectRaw('course_sessions.end_date as end_date_value')
                    ->selectRaw('course_sessions.created_at as created_at_value')
                    ->selectRaw("$scoreExpr as score")
                    ->selectRaw("$occupancyExpr as occupancy_value")
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

        $thumbnailUrl = null;

        if ($course->thumbnail) {
            $thumbnailUrl = RvMedia::getImageUrl(
                $course->thumbnail,
                'thumb',
                false
            );
        }

        if (! $thumbnailUrl || $thumbnailUrl === RvMedia::getDefaultImage()) {
            $thumbnailUrl = null;
        }

        $courseName = BaseHelper::clean($course->name ?? '—');
        $courseUrl = route('course.edit', $course->getKey());

        $metaParts = array_filter([
            $course->category?->name ? BaseHelper::clean($course->category->name) : null,
            $course->instructor?->name ? BaseHelper::clean($course->instructor->name) : null,
        ]);

        $metaHtml = $metaParts
            ? '<div class="course-session-meta text-muted">' . implode(' • ', $metaParts) . '</div>'
            : '';

        $imageAlt = e(trans('plugins/courses::courses.table.thumbnail_alt', ['course' => $courseName]));
        $fallbackAlt = e(trans('plugins/courses::courses.table.thumbnail_fallback_alt', ['course' => $courseName]));

        $thumbnailContent = $thumbnailUrl
            ? '<img src="' . $thumbnailUrl . '" alt="' . $imageAlt . '" loading="lazy">'
            : '<span class="course-session-thumbnail__icon" aria-hidden="true">'
                . $this->renderFallbackThumbnailIcon($courseName)
                . '</span>';

        $thumbnailAttributes = $thumbnailUrl
            ? 'class="course-session-thumbnail"'
            : 'class="course-session-thumbnail course-session-thumbnail--fallback" role="img" aria-label="' . $fallbackAlt . '"';

        return <<<HTML
<div class="course-session-card d-flex align-items-center gap-3">
    <div {$thumbnailAttributes}>
        {$thumbnailContent}
    </div>
    <div class="course-session-header flex-grow-1">
        <a href="{$courseUrl}" class="course-session-title">{$courseName}</a>
        {$metaHtml}
    </div>
</div>
HTML;
    }

    protected function renderFallbackThumbnailIcon(string $courseName): string
    {
        $decoded = trim(html_entity_decode(strip_tags($courseName), ENT_QUOTES, 'UTF-8'));
        $firstChar = $decoded !== '' ? mb_substr($decoded, 0, 1, 'UTF-8') : '•';
        $initial = mb_strtoupper($firstChar, 'UTF-8');
        $initialEscaped = e($initial);

        return <<<HTML
<span class="course-session-thumbnail__initial">{$initialEscaped}</span>
<svg class="course-session-thumbnail__glyph" viewBox="0 0 24 24" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
    <path d="M4 7a3 3 0 0 1 3-3h10a3 3 0 0 1 3 3v10a3 3 0 0 1-3 3H7a3 3 0 0 1-3-3V7Z" fill="currentColor" opacity="0.12" />
    <path d="M9.25 9.5a1.75 1.75 0 1 0 3.5 0a1.75 1.75 0 0 0-3.5 0Z" fill="currentColor" />
    <path d="M6.5 16.5c0-1.66 1.97-3 4.5-3s4.5 1.34 4.5 3" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" fill="none" />
</svg>
HTML;
    }

    protected function renderPrice(CourseSession $session): string
    {
        $course = $session->course;

        if (! $course || $course->price === null) {
            return '<span class="course-session-price course-session-price--muted">—</span>';
        }

        return '<span class="course-session-price" aria-label="' . e(trans('plugins/courses::courses.table.price_label', ['price' => format_price($course->price)])) . '">' . format_price($course->price) . '</span>';
    }

    protected function renderViews(CourseSession $session): string
    {
        $stats = $this->performance()->forSession($session);
        $viewsSource = $stats['views_source'] ?? 'none';
        $views = (int) ($stats['views'] ?? 0);

        $displayValue = $viewsSource === 'none' ? '—' : number_format($views, 0, ',', '.');
        $sourceLabel = match ($viewsSource) {
            'analytics' => trans('plugins/courses::courses.table.views_source_analytics', ['days' => $stats['analytics_days'] ?? $this->performance()->analyticsDays()]),
            'metadata' => trans('plugins/courses::courses.table.views_source_metadata'),
            default => trans('plugins/courses::courses.table.views_hint_unavailable'),
        };

        $title = e(trans('plugins/courses::courses.table.views_label', ['count' => $displayValue]));

        $icon = <<<SVG
<svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
    <path d="M12 5C6.5 5 2.73 8.11 1 12c1.73 3.89 5.5 7 11 7s9.27-3.11 11-7c-1.73-3.89-5.5-7-11-7Z" stroke="currentColor" stroke-width="1.5" fill="none"/>
    <circle cx="12" cy="12" r="3" fill="currentColor" />
</svg>
SVG;

        return <<<HTML
<div class="course-session-views" role="text" aria-label="{$title}">
    <span class="course-session-badge" title="{$sourceLabel}">
        <span class="course-session-badge__icon">{$icon}</span>
        <span class="course-session-badge__value">{$displayValue}</span>
    </span>
</div>
HTML;
    }

    protected function renderOccupancy(CourseSession $session): string
    {
        $stats = $this->performance()->forSession($session);
        $booked = (int) $stats['booked_seats'];
        $maxSeats = $stats['max_seats'];

        if ($maxSeats !== null) {
            $capacity = max($maxSeats, 1);
            $ratio = $capacity > 0 ? $booked / $capacity : 0;
            $ratioLabel = $booked . ' / ' . $capacity;
        } else {
            $ratio = $booked > 0 ? 1 : 0;
            $ratioLabel = $booked . ' / ∞';
        }

        $chairs = 10;
        $filledChairs = (int) round(min(max($ratio, 0), 1) * $chairs);
        $filledChairs = max(0, min($chairs, $filledChairs));

        $icons = '';

        for ($i = 0; $i < $chairs; $i++) {
            $isFilled = $i < $filledChairs;
            $seatFill = $isFilled ? '#2F6A62' : '#F5FBF9';
            $seatStroke = $isFilled ? '#2F6A62' : '#C7DAD6';

            $icons .= <<<SVG
<span class="course-session-seat" aria-hidden="true">
    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" role="img">
        <rect x="5" y="8" width="14" height="7" rx="2.5" fill="{$seatFill}" stroke="{$seatStroke}" stroke-width="1.4" />
        <rect x="7" y="15" width="10" height="5" rx="2" fill="{$seatFill}" stroke="{$seatStroke}" stroke-width="1.4" />
        <path d="M6 20h2" stroke="{$seatStroke}" stroke-width="1.4" stroke-linecap="round" />
        <path d="M16 20h2" stroke="{$seatStroke}" stroke-width="1.4" stroke-linecap="round" />
    </svg>
</span>
SVG;
        }

        $progress = max(0, min(100, (int) round($ratio * 100)));
        $accessibilityLabel = e(trans('plugins/courses::courses.table.occupancy_label', [
            'booked' => $booked,
            'max' => $maxSeats ?? '∞',
        ]));

        $seatLabel = e(trans('plugins/courses::courses.table.occupancy_ratio_label', ['ratio' => $ratioLabel]));

        return <<<HTML
<div class="course-session-occupancy" role="group" aria-label="{$accessibilityLabel}">
    <div class="course-session-occupancy__row">
        <div class="course-session-occupancy__icons" aria-hidden="true">{$icons}</div>
        <span class="course-session-occupancy__ratio" aria-label="{$seatLabel}">{$ratioLabel}</span>
    </div>
    <div class="course-session-occupancy__progress" aria-hidden="true">
        <span style="width: {$progress}%;"></span>
    </div>
</div>
HTML;
    }

    protected function renderParticipants(CourseSession $session): string
    {
        $buttonLabel = e(trans('plugins/courses::courses.table.view_participants'));
        $sessionId = $session->getKey();

        return <<<HTML
<button type="button" class="course-session-button view-participants-btn" data-session-id="{$sessionId}">
    <span class="course-session-button__icon" aria-hidden="true">
        <svg viewBox="0 0 24 24" width="18" height="18" xmlns="http://www.w3.org/2000/svg">
            <path d="M16.5 7a3.5 3.5 0 1 1-7 0a3.5 3.5 0 0 1 7 0Z" fill="currentColor"/>
            <path d="M4 18.5c0-2.21 2.91-4 6.5-4s6.5 1.79 6.5 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" fill="none"/>
            <path d="M17.5 10.5a2.5 2.5 0 1 1 0 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" fill="none"/>
            <path d="M21 18.5c0-1.39-1.56-2.58-3.75-3.1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" fill="none"/>
        </svg>
    </span>
    <span class="course-session-button__label">{$buttonLabel}</span>
</button>
HTML;
    }

    protected function renderDate(CourseSession $session): string
    {
        $startDate = $this->formatDate($session->start_date, 'd.m.Y');
        $weekday = $session->start_date
            ? '<span class="course-session-date__weekday">' . e($session->start_date->locale(app()->getLocale())->translatedFormat('l')) . '</span>'
            : '';

        return <<<HTML
<div class="course-session-date">
    <span class="course-session-date__value">{$startDate}</span>
    {$weekday}
</div>
HTML;
    }

    protected function renderTime(CourseSession $session): string
    {
        $timeRange = '—';

        if ($session->start_date && $session->end_date) {
            $timeRange = $session->start_date->format('H:i') . ' – ' . $session->end_date->format('H:i');
        } elseif ($session->start_date) {
            $timeRange = $session->start_date->format('H:i');
        }

        $timeHtml = $timeRange !== '—'
            ? '<span class="course-session-time" aria-label="' . e(trans('plugins/courses::courses.table.time_label', ['time' => $timeRange])) . '">' . $timeRange . '</span>'
            : '<span class="course-session-time course-session-time--muted">' . $timeRange . '</span>';

        return <<<HTML
<div class="course-session-time-wrapper">
    {$timeHtml}
</div>
HTML;
    }

    protected function renderCapacity(CourseSession $session): string
    {
        if ($session->course?->unlimited_seats) {
            return '<span class="course-session-capacity">∞</span>';
        }

        if ($session->available_seats !== null) {
            return '<span class="course-session-capacity">' . number_format((int) $session->available_seats, 0, ',', '.') . '</span>';
        }

        $stats = $this->performance()->forSession($session);
        $maxSeats = $stats['max_seats'];

        if ($maxSeats !== null) {
            return '<span class="course-session-capacity">' . number_format((int) $maxSeats, 0, ',', '.') . '</span>';
        }

        return '<span class="course-session-capacity">—</span>';
    }

    protected function renderCreatedAt(CourseSession $session): string
    {
        $created = $session->created_at ?? $session->getAttribute('created_at_value');

        if ($created instanceof CarbonInterface) {
            return '<span class="course-session-created">' . $created->format('d.m.Y') . '</span>';
        }

        if (is_string($created) && $created !== '') {
            try {
                $date = \Carbon\Carbon::parse($created);

                return '<span class="course-session-created">' . $date->format('d.m.Y') . '</span>';
            } catch (\Exception) {
                // ignore parsing issues
            }
        }

        return '<span class="course-session-created">—</span>';
    }

    protected function renderScore(CourseSession $session): string
    {
        $stats = $this->performance()->forSession($session);
        $score = number_format($stats['score'], 1);

        [$background, $accent, $textColor, $ratingKey] = $this->resolveScoreStyle((float) $stats['score']);
        $ratingText = trans($ratingKey);
        $ratingLabel = BaseHelper::clean($ratingText);
        $title = e($ratingText);

        return <<<HTML
<span class="course-session-score" style="--course-session-score-bg: {$background}; --course-session-score-fg: {$textColor}; --course-session-score-ring: {$accent};" title="{$title}" aria-label="{$ratingLabel}">
    {$score}
</span>
HTML;
    }

    protected function resolveScoreStyle(float $score): array
    {
        return match (true) {
            $score >= 85 => ['#2F6A62', '#91D0C5', '#FFFFFF', 'plugins/courses::courses.table.score_rating.great'],
            $score >= 65 => ['#3E8F80', '#A8DCD2', '#FFFFFF', 'plugins/courses::courses.table.score_rating.good'],
            $score >= 45 => ['#F7D8A4', '#F2B138', '#553C1C', 'plugins/courses::courses.table.score_rating.fair'],
            default => ['#F1B8B0', '#DE6F64', '#4A1B15', 'plugins/courses::courses.table.score_rating.poor'],
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

    protected function versionedStylesheet(): string
    {
        $relativePath = 'vendor/core/plugins/courses/css/course-session-table.css';
        $absolutePath = public_path($relativePath);

        if (file_exists($absolutePath)) {
            return $relativePath . '?v=' . filemtime($absolutePath);
        }

        return $relativePath . '?v=' . static::STYLESHEET_FALLBACK_VERSION;
    }
}
