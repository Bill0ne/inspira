<?php

namespace Botble\Courses\Tables;

use Botble\Courses\Enums\CourseStatusEnum;
use Botble\Courses\Supports\CourseStatusManager;
use Botble\Base\Facades\Assets;
use Botble\Base\Facades\BaseHelper;
use Botble\Courses\Models\Course;
use Botble\Courses\Models\CourseSession;
use Botble\Courses\Models\Instructor;
use Botble\Courses\Services\CoursePerformanceService;
use Botble\Media\Facades\RvMedia;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\BulkActions\DeleteBulkAction;
use Botble\Table\BulkChanges\SelectBulkChange;
use Botble\Table\Columns\FormattedColumn;
use Botble\Table\Columns\IdColumn;
use Illuminate\Database\Eloquent\Builder;

class CourseSessionTable extends TableAbstract
{
    protected ?CoursePerformanceService $performanceService = null;

    public function setup(): void
    {
        // WICHTIG: Initialisiert u. a. den Teilnehmer-Dialog (.view-participants-btn)
        Assets::addScriptsDirectly(['vendor/core/plugins/courses/js/script.js']);
        $this->hasOperations = false;
        $this->defaultSortColumnName = 'course_sessions.start_date';

        $this
            ->model(CourseSession::class)
            ->addColumns([
                IdColumn::make(),

                // Kurs (Bild + Titel + Teilnehmer-Button)
                FormattedColumn::make('session_overview')
                    ->title('Kurs')
                    ->orderable(false)
                    ->searchable(false)
                    ->escape(false)
                    ->getValueUsing(fn ($col) => $this->renderSessionOverview($col->getItem())),

                // Coach
                FormattedColumn::make('coach')
                    ->title('Coach')
                    ->orderable(false)
                    ->searchable(false)
                    ->escape(false)
                    ->getValueUsing(fn ($col) => $this->renderCoach($col->getItem())),

                // Kategorie
                FormattedColumn::make('category')
                    ->title('Kategorie')
                    ->orderable(false)
                    ->searchable(false)
                    ->escape(false)
                    ->getValueUsing(fn ($col) => $this->renderCategory($col->getItem())),

                // Status
                FormattedColumn::make('course_status')
                    ->title('Status')
                    ->orderable(false)
                    ->searchable(false)
                    ->escape(false)
                    ->getValueUsing(fn ($col) => $this->renderCourseStatus($col->getItem())),

                // Preis
                FormattedColumn::make('price')
                    ->title('Preis')
                    ->orderable(false)
                    ->searchable(false)
                    ->getValueUsing(fn ($col) => $this->renderPrice($col->getItem())),

                // Belegung (10 Stühle)
                FormattedColumn::make('occupancy')
                    ->title('Belegung')
                    ->orderable(false)
                    ->searchable(false)
                    ->escape(false)
                    ->getValueUsing(fn ($col) => $this->renderOccupancy($col->getItem())),

                // Datum
                FormattedColumn::make('date')
                    ->title('Datum')
                    ->data('date')
                    ->name('course_sessions.start_date')
                    ->orderable(true)
                    ->searchable(false)
                    ->escape(false)
                    ->getValueUsing(fn ($col) => $this->renderDate($col->getItem())),

                // Uhrzeit
                FormattedColumn::make('time')
                    ->title('Uhrzeit')
                    ->orderable(false)
                    ->searchable(false)
                    ->escape(false)
                    ->getValueUsing(fn ($col) => $this->renderTime($col->getItem())),

                // Score (ohne Untertext)
                FormattedColumn::make('score')
                    ->title('Score')
                    ->orderable(false)
                    ->searchable(false)
                    ->escape(false)
                    ->getValueUsing(fn ($col) => $this->renderScore($col->getItem())),
            ])
            ->addBulkActions([
                DeleteBulkAction::make()->permission('course-sessions.destroy'),
            ])
            ->addFilters([
                SelectBulkChange::make()
                    ->name('course_status')
                    ->title('Status')
                    ->choices(CourseStatusManager::labels()),
                SelectBulkChange::make()
                    ->name('instructor_id')
                    ->title('Coach')
                    ->choices(
                        Instructor::query()
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->all()
                    ),
            ])
            ->queryUsing(function (Builder $query) {
                return $query
                    ->with(['course.category:id,name', 'course.instructor:id,name'])
                    ->withCount([
                        // aktive Buchungen (für „booked“)
                        'bookings as active_bookings_count',
                    ])
                    ->select([
                        'id',
                        'course_id',
                        'start_date',
                        'end_date',
                        'available_seats', // pro Sitzungs-Record (kann NULL für unlimited sein)
                        'created_at',
                    ])
                    // DataTables benötigt eine tatsächliche Daten-Spalte „date“, da die
                    // FormattedColumn weiter oben ->data('date') registriert.
                    ->addSelect(['course_sessions.start_date as date']);
            })
            ->onFilterQuery(function ($query, $key, $operator, $value) {
                if (! $value) {
                    return false;
                }

                if ($key === 'course_status') {
                    return $query->whereHas('course', function ($subQuery) use ($value) {
                        $subQuery->where('status', $value);
                    });
                }

                if ($key === 'instructor_id') {
                    return $query->whereHas('course', function ($subQuery) use ($value) {
                        $subQuery->where('instructor_id', $value);
                    });
                }

                return false;
            });
    }

    public function shouldShowFilterSection(): bool
    {
        $request = $this->request() ?? request();

        if ($request && optional($request->attributes)->get('course_session_default_filter')) {
            return false;
        }

        return $this->isFiltering();
    }

    protected function renderSessionOverview(CourseSession $session): string
    {
        $course = $session->course;
        if (! $course) {
            return '<span class="text-muted">—</span>';
        }

        // Fallback-Bild sicher
        $thumb = RvMedia::getImageUrl(
            $course->thumbnail,
            'thumb',
            false,
            RvMedia::getImageUrl('default-course.jpg', 'thumb', false, RvMedia::getDefaultImage())
        );

        $name = BaseHelper::clean($course->name ?? '—');
        $editUrl = route('course.edit', $course->getKey());
        $btnLabel = __('Teilnehmer anzeigen');

        return <<<HTML
<div class="d-flex align-items-center gap-3">
  <div class="flex-shrink-0">
    <img src="{$thumb}" alt="{$name}" class="rounded" style="width:56px;height:56px;object-fit:cover;">
  </div>
  <div class="flex-grow-1">
    <a href="{$editUrl}" class="fw-semibold text-body text-decoration-none d-block">{$name}</a>
    <button class="btn btn-outline-primary btn-sm mt-2 view-participants-btn"
            data-session-id="{$session->getKey()}">{$btnLabel}</button>
  </div>
</div>
HTML;
    }

    protected function renderCoach(CourseSession $session): string
    {
        $coach = $session->course?->instructor?->name;
        return $coach
            ? "<span class='badge bg-light text-dark border px-3 py-1'>{$coach}</span>"
            : '—';
    }

    protected function renderCategory(CourseSession $session): string
    {
        $cat = $session->course?->category?->name;
        return $cat
            ? "<span class='badge bg-light text-dark border px-3 py-1'>{$cat}</span>"
            : '—';
    }

    protected function renderPrice(CourseSession $session): string
    {
        $price = $session->course?->price;
        return $price ? format_price($price) : '—';
    }

    protected function renderCourseStatus(CourseSession $session): string
    {
        $course = $session->course;

        if (! $course) {
            return '—';
        }

        $status = $course->status;

        $status = CourseStatusManager::normalize($status);

        if (! $status instanceof CourseStatusEnum) {
            return '—';
        }

        return (string) (CourseStatusManager::getDisplayStatus($course) ?? $status)->toHtml();
    }

    // 10 SVG-Stühle; Füllung basierend auf gebucht vs. maxSeats (oder Prozent bei „unlimited“)
    protected function renderOccupancy(CourseSession $session): string
    {
        $stats   = $this->performance()->forSession($session);
        $booked  = (int)($stats['booked_seats'] ?? 0);
        $max     = $stats['max_seats'];                  // NULL = unlimited
        $percent = (float)($stats['occupancy_percent'] ?? 0);

        if ($max !== null && $max > 0) {
            $filledChairs = (int)round(10 * min(1, $booked / $max));
            $label = "{$booked}/{$max}";
        } else {
            $filledChairs = (int)round(10 * min(1, $percent / 100));
            $label = (string)$booked; // unlimited -> nur gebucht anzeigen
        }

        $chairSvg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16">
  <path d="M6 10V5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v5h1a1 1 0 0 1 0 2h-1v7a1 1 0 0 1-2 0v-7H8v7a1 1 0 0 1-2 0v-7H5a1 1 0 0 1 0-2h1z" fill="currentColor"/>
</svg>
SVG;

        $row = '';
        for ($i = 1; $i <= 10; $i++) {
            $color = $i <= $filledChairs ? '#578E88' : '#D1D5DB';
            $row  .= "<span style=\"color:{$color};margin-right:2px;\">{$chairSvg}</span>";
        }

        return "<div class='d-flex align-items-center'>{$row}<span class='ms-2 small text-muted'>{$label}</span></div>";
    }

    protected function renderDate(CourseSession $session): string
    {
        return $session->start_date ? $session->start_date->format('d.m.Y') : '—';
    }

    protected function renderTime(CourseSession $session): string
    {
        if (! $session->start_date || ! $session->end_date) {
            return '—';
        }

        return $session->start_date->format('H:i') . ' – ' . $session->end_date->format('H:i');
    }

    protected function renderScore(CourseSession $session): string
    {
        $stats    = $this->performance()->forSession($session);
        $score    = (int)($stats['score'] ?? 0);
        $percent  = max(0, min(100, (int) round($score)));

        return <<<HTML
<div class="d-flex justify-content-center">
  <div style="width:48px;height:48px;border-radius:50%;
              background:conic-gradient(#578E88 {$percent}%, #e9ecef {$percent}%);
              display:flex;align-items:center;justify-content:center;
              font-weight:600;color:#2b2b2b;">
    {$score}
  </div>
</div>
HTML;
    }

    protected function performance(): CoursePerformanceService
    {
        return $this->performanceService ??= app(CoursePerformanceService::class);
    }
}
