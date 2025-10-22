<?php

namespace Botble\Courses\Tables;

use Botble\Courses\Models\CourseSession;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\BulkActions\DeleteBulkAction;
use Botble\Table\BulkChanges\CreatedAtBulkChange;
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\DateColumn;
use Botble\Table\Columns\FormattedColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseSessionTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(CourseSession::class)
            ->addColumns([
                IdColumn::make(),

                FormattedColumn::make('course_id')
                    ->title(trans('plugins/courses::courses.course.name'))
                    ->getValueUsing(function (FormattedColumn $column) {
                        return $column->getItem()->course?->name ?? '—';
                    }),

                // Seats + Progressbar
                FormattedColumn::make('seats')
                    ->title(trans('plugins/courses::courses.course-session.seats'))
                    ->escape(false) // HTML für den Balken erlauben
                    ->getValueUsing(function (FormattedColumn $column) {
                        $session = $column->getItem();

                        // Unlimited (keine Kapazität gepflegt)
                        if (is_null($session->available_seats)) {
                            return 'Unlimited';
                        }

                        // Buchungen zählen (1 Buchung = 1 belegter Platz)
                        $booked = (int) $session->bookings()->count();
                        $capacity = (int) $session->available_seats;
                        $remaining = max(0, $capacity - $booked);

                        // Prozent berechnen
                        $percent = $capacity > 0
                            ? (int) round(min(100, ($booked / (float) $capacity) * 100))
                            : 0;

                        // Label (z. B. "3 used / 7 remaining" ODER "3 / 10")
                        $label = "{$booked} used / {$remaining} remaining";

                        // Kompakter Progress-Balken (#578E88 wie Inspira)
                        $bar = sprintf(
                            '<div style="margin-top:6px;background:#e9ecef;height:6px;border-radius:4px;overflow:hidden;">
                                <div style="width:%1$d%%;height:6px;border-radius:4px;background:#578E88;"></div>
                             </div>',
                            $percent
                        );

                        return $label . $bar;
                    }),

                DateColumn::make('start_date')
                    ->title(trans('plugins/courses::courses.course-session.start_date'))
                    ->dateFormat('d-m-Y H:i'),

                DateColumn::make('end_date')
                    ->title(trans('plugins/courses::courses.course-session.end_date'))
                    ->dateFormat('d-m-Y H:i'),

                FormattedColumn::make('available_seats')
                    ->title(trans('plugins/courses::courses.course-session.available_seats'))
                    ->getValueUsing(fn (FormattedColumn $column) => $column->getItem()->available_seats ?? 'Unlimited'),

                CreatedAtColumn::make(),
            ])
            ->addBulkActions([
                DeleteBulkAction::make()->permission('course-sessions.destroy'),
            ])
            ->addBulkChanges([
                CreatedAtBulkChange::make(),
            ])
            ->queryUsing(function (Builder $query) {
                return $query
                    ->with(['course' => function (BelongsTo $query) {
                        $query->select(['id', 'name']);
                    }])
                    ->select([
                        'id',
                        'course_id',
                        'start_date',
                        'end_date',
                        'available_seats',
                        'created_at',
                    ]);
            });
    }

    public function hasOperations(): bool
    {
        return false;
    }
}
