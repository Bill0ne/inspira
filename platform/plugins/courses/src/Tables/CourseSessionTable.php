<?php

namespace Botble\Courses\Tables;

use Botble\Base\Facades\BaseHelper;
use Botble\Courses\Models\CourseSession;
use Botble\Courses\Services\CoursePerformanceService;
use Botble\Hotel\Enums\BookingStatusEnum;
use Botble\Media\Facades\RvMedia;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\BulkActions\DeleteBulkAction;
use Botble\Table\Columns\FormattedColumn;
use Botble\Table\Columns\IdColumn;
use Illuminate\Database\Eloquent\Builder;

class CourseSessionTable extends TableAbstract
{
    protected ?CoursePerformanceService $performanceService = null;

    public function setup(): void
    {
        $this
            ->model(CourseSession::class)
            ->addColumns([
                IdColumn::make(),

                // === Kurs-Infos (Name + Bild)
                FormattedColumn::make('session_overview')
                    ->title('Kurs')
                    ->orderable(false)
                    ->searchable(false)
                    ->escape(false)
                    ->getValueUsing(fn($col) => $this->renderSessionOverview($col->getItem())),

                // === Coach (Instructor)
                FormattedColumn::make('coach')
                    ->title('Coach')
                    ->orderable(false)
                    ->searchable(false)
                    ->escape(false)
                    ->getValueUsing(fn($col) => $this->renderCoach($col->getItem())),

                // === Kategorie
                FormattedColumn::make('category')
                    ->title('Kategorie')
                    ->orderable(false)
                    ->searchable(false)
                    ->escape(false)
                    ->getValueUsing(fn($col) => $this->renderCategory($col->getItem())),

                // === Preis
                FormattedColumn::make('price')
                    ->title('Preis')
                    ->orderable(false)
                    ->searchable(false)
                    ->getValueUsing(fn($col) => $this->renderPrice($col->getItem())),

                // === Belegung
                FormattedColumn::make('occupancy')
                    ->title('Belegung')
                    ->orderable(false)
                    ->searchable(false)
                    ->escape(false)
                    ->getValueUsing(fn($col) => $this->renderOccupancy($col->getItem())),

                // === Datum
                FormattedColumn::make('date')
                    ->title('Datum')
                    ->orderable(false)
                    ->searchable(false)
                    ->escape(false)
                    ->getValueUsing(fn($col) => $this->renderDate($col->getItem())),

                // === Uhrzeit
                FormattedColumn::make('time')
                    ->title('Uhrzeit')
                    ->orderable(false)
                    ->searchable(false)
                    ->escape(false)
                    ->getValueUsing(fn($col) => $this->renderTime($col->getItem())),

                // === Score
                FormattedColumn::make('score')
                    ->title('Score')
                    ->orderable(false)
                    ->searchable(false)
                    ->escape(false)
                    ->getValueUsing(fn($col) => $this->renderScore($col->getItem())),
            ])
            ->addBulkActions([
                DeleteBulkAction::make()->permission('course-sessions.destroy'),
            ])
            ->queryUsing(function (Builder $query) {
                return $query
                    ->with(['course.category:id,name', 'course.instructor:id,name'])
                    ->withCount(['bookings as active_bookings_count'])
                    ->select(['id', 'course_id', 'start_date', 'end_date', 'available_seats', 'created_at']);
            });
    }

    // === Kursname + Thumbnail
    protected function renderSessionOverview(CourseSession $session): string
    {
        $course = $session->course;
        if (!$course) return '<span class="text-muted">—</span>';

        $thumbnail = RvMedia::getImageUrl($course->thumbnail, 'thumb', false, RvMedia::getDefaultImage());
        $name = BaseHelper::clean($course->name ?? '—');
        $url = route('course.edit', $course->getKey());

        return <<<HTML
<div class="d-flex align-items-center gap-3">
  <div class="flex-shrink-0">
    <img src="{$thumbnail}" alt="{$name}" class="rounded" style="width:56px;height:56px;object-fit:cover;">
  </div>
  <div class="flex-grow-1">
    <a href="{$url}" class="fw-semibold text-body text-decoration-none">{$name}</a>
  </div>
</div>
HTML;
    }

    protected function renderCoach(CourseSession $session): string
    {
        $coach = $session->course?->instructor?->name;
        return $coach ? "<span class='badge bg-light text-dark border px-3 py-1'>{$coach}</span>" : '—';
    }

    protected function renderCategory(CourseSession $session): string
    {
        $cat = $session->course?->category?->name;
        return $cat ? "<span class='badge bg-light text-dark border px-3 py-1'>{$cat}</span>" : '—';
    }

    protected function renderPrice(CourseSession $session): string
    {
        $price = $session->course?->price;
        return $price ? format_price($price) : '—';
    }

    // === Belegung mit 10 Stühlen
    protected function renderOccupancy(CourseSession $session): string
    {
        $stats = $this->performance()->forSession($session);
        $filled = (int) round(($stats['occupancy_percent'] / 100) * 10);
        $chairs = '';
        for ($i = 1; $i <= 10; $i++) {
            $color = $i <= $filled ? '#578E88' : '#ccc';
            $chairs .= "<span style='color:{$color};margin-right:3px;'>🪑</span>";
        }
        return "<div class='small'>{$chairs}</div>";
    }

    protected function renderDate(CourseSession $session): string
    {
        return $session->start_date ? $session->start_date->format('d.m.Y') : '—';
    }

    protected function renderTime(CourseSession $session): string
    {
        if (!$session->start_date || !$session->end_date) return '—';
        return $session->start_date->format('H:i') . ' – ' . $session->end_date->format('H:i');
    }

    protected function renderScore(CourseSession $session): string
    {
        $stats = $this->performance()->forSession($session);
        $score = $stats['score'];
        $percent = max(0, min(100, (int) round($score)));

        return <<<HTML
<div class="d-flex justify-content-center">
  <div style="width:48px;height:48px;border-radius:50%;background:conic-gradient(#578E88 {$percent}%, #e9ecef {$percent}%);display:flex;align-items:center;justify-content:center;font-weight:600;color:#2b2b2b;">
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
