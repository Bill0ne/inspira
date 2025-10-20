<?php

namespace Botble\Courses\Tables;

use Botble\Base\Facades\Assets;
use Botble\Courses\Models\CourseSession;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\BulkActions\DeleteBulkAction;
use Botble\Table\BulkChanges\CreatedAtBulkChange;
use Botble\Table\BulkChanges\SelectBulkChange;
use Botble\Table\BulkChanges\DateBulkChange;
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\DateColumn;
use Botble\Table\Columns\FormattedColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Relation as EloquentRelation;
use Illuminate\Database\Query\Builder as QueryBuilder;

class CourseSessionTable extends TableAbstract
{
    public function setup(): void
    {
        Assets::addScriptsDirectly(['vendor/core/plugins/courses/js/script.js']);

        $this
            ->model(CourseSession::class)
            ->addColumns([
                IdColumn::make(),

                FormattedColumn::make('course_id')
                    ->title(trans('plugins/courses::courses.course.name'))
                    ->getValueUsing(function (FormattedColumn $column) {
                        return $column->getItem()->course?->name ?? '—';
                    }),

                FormattedColumn::make('view_participants')
                    ->title(trans('Teilnehmerdetails'))
                    ->escape(false)
                    ->orderable(false)
                    ->getValueUsing(function (FormattedColumn $column) {
                        $session = $column->getItem();

                        return '<button class="btn btn-info btn-sm view-participants-btn" data-session-id="'
                            . $session->id . '">Sicht</button>';
                    }),

                FormattedColumn::make('seats')
                    ->title(trans('plugins/courses::courses.course-session.seats'))
                    ->orderable(false)
                    ->escape(false)
                    ->getValueUsing(function (FormattedColumn $column) {
                        $session = $column->getItem();

                        if (is_null($session->available_seats)) {
                            return 'Unbegrenzt';
                        }

                        $booked = (int) $session->bookings()->count();
                        $capacity = (int) $session->available_seats;
                        $remaining = max(0, $capacity - $booked);

                        $percent = $capacity > 0
                            ? (int) round(min(100, ($booked / (float) $capacity) * 100))
                            : 0;

                        $label = "{$booked} gebraucht / {$remaining} übrig";

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
                    ->getValueUsing(fn (FormattedColumn $column) => $column->getItem()->available_seats ?? 'Unbegrenzt'),

                CreatedAtColumn::make(),
            ])
            ->addBulkActions([
                DeleteBulkAction::make()->permission('course-sessions.destroy'),
            ])
            ->addBulkChanges([
                SelectBulkChange::make() ->name('course_id') ->title(trans('plugins/courses::courses.course.name')) ->choices(\Botble\Courses\Models\Course::query()->pluck('name', 'id')->all()), DateBulkChange::make() ->name('start_date') ->title(trans('plugins/courses::courses.course.start_date')), DateBulkChange::make() ->name('end_date') ->title(trans('plugins/courses::courses.course.end_date')),
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
            })->onFilterQuery(function (
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
        ;
    }

    public function hasOperations(): bool
    {
        return false;
    }
}
