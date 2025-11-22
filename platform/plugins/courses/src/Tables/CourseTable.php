<?php

namespace Botble\Courses\Tables;

use Botble\Courses\Models\Course;
use Botble\Courses\Supports\CourseStatusManager;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\Actions\EditAction;
use Botble\Table\BulkActions\DeleteBulkAction;
use Botble\Table\BulkChanges\CreatedAtBulkChange;
use Botble\Table\BulkChanges\NameBulkChange;
use Botble\Table\BulkChanges\SelectBulkChange;
use Botble\Table\BulkChanges\DateBulkChange;
use Botble\Table\BulkChanges\TextBulkChange;
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\DateColumn;
use Botble\Table\Columns\NameColumn;
use Botble\Table\Columns\StatusColumn;
use Botble\Table\Columns\FormattedColumn;
use Botble\Table\HeaderActions\CreateHeaderAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Relation as EloquentRelation;
use Illuminate\Database\Query\Builder as QueryBuilder;

class CourseTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(Course::class)
            ->addHeaderAction(CreateHeaderAction::make()->route('course.create'))
            ->addActions([
                EditAction::make()->route('course.edit'),
                DeleteAction::make()->route('course.destroy'),
            ])
            ->addColumns([
                IdColumn::make(),
                NameColumn::make()->route('course.edit'),
                FormattedColumn::make('instructor_id')
                    ->title(trans('plugins/courses::courses.instructor.instructor'))
                    ->getValueUsing(function (FormattedColumn $column) {
                        return $column->getItem()->instructor?->name ?? '—';
                    }),
                FormattedColumn::make('category_id')
                    ->title(trans('plugins/courses::courses.course-category.category'))
                    ->getValueUsing(function (FormattedColumn $column) {
                        return $column->getItem()->category?->name ?? '—';
                    }),
                FormattedColumn::make('sessions_count')
                    ->title('Sessions')
                    ->getValueUsing(function (FormattedColumn $column) {
                        return $column->getItem()->sessions()->count();
                    })->searchable(false),
                FormattedColumn::make('duration')
                    ->title(trans('plugins/courses::courses.course.duration'))
                    ->getValueUsing(fn (FormattedColumn $column) => $column->getItem()->duration ?? '—'),
                FormattedColumn::make('start_date')
                    ->title(trans('plugins/courses::courses.course.start_date')),
                FormattedColumn::make('end_date')
                    ->title(trans('plugins/courses::courses.course.end_date')),
                CreatedAtColumn::make(),
                StatusColumn::make()
                    ->title(trans('core/base::tables.status'))
                    ->getValueUsing(function (StatusColumn $column, $value) {
                        $course = $column->getItem();

                        if (! $course instanceof Course) {
                            return $value;
                        }

                        $status = CourseStatusManager::normalize($value ?? $course->status);

                        return CourseStatusManager::getDisplayStatus($course) ?? $status;
                    }),
            ])
            ->addBulkActions([
                DeleteBulkAction::make()->permission('course.destroy'),
            ])
            ->addBulkChanges([
                NameBulkChange::make(),
                TextBulkChange::make()
                    ->name('duration')
                    ->title(trans('plugins/courses::courses.course.duration')),
                SelectBulkChange::make()
                    ->name('instructor_id')
                    ->title(trans('plugins/courses::courses.instructor.instructor'))
                    ->choices(\Botble\Courses\Models\Instructor::query()->pluck('name', 'id')->all()),
                SelectBulkChange::make()
                    ->name('category_id')
                    ->title(trans('plugins/courses::courses.course-category.category'))
                    ->choices(\Botble\Courses\Models\CourseCategory::query()->pluck('name', 'id')->all()),
                DateBulkChange::make()
                    ->name('start_date')
                    ->title(trans('plugins/courses::courses.course.start_date')),
                DateBulkChange::make()
                    ->name('end_date')
                    ->title(trans('plugins/courses::courses.course.end_date')),
                SelectBulkChange::make()
                    ->name('status')
                    ->title(trans('core/base::tables.status'))
                    ->choices(CourseStatusManager::labels())
                    ->validate(['required', 'in:' . implode(',', CourseStatusManager::values())]),
                CreatedAtBulkChange::make(),
            ])
            ->queryUsing(function (Builder $query) {
                return $query
                    ->with([
                        'instructor' => function (BelongsTo $query) {
                            $query->select(['id', 'name']);
                        },
                        'category' => function (BelongsTo $query) {
                            $query->select(['id', 'name']);
                        },
                    ])
                    ->select([
                        'id',
                        'name',
                        'duration',
                        'start_date',
                        'end_date',
                        'instructor_id',
                        'category_id',
                        'created_at',
                        'status',
                    ]);
            })
            ->onAjax(function (self $table) {
                return $table->toJson(
                    $table
                        ->table
                        ->eloquent($table->query())
                        ->filter(function ($query) {
                            if ($keyword = $this->request->input('search.value')) {
                                $keyword = '%' . strtolower($keyword) . '%';

                                return $query->where(function ($q) use ($keyword) {
                                    $q->whereRaw('LOWER(courses.name) LIKE ?', [$keyword])
                                        ->orWhereRaw('LOWER(courses.duration) LIKE ?', [$keyword])
                                        ->orWhereRaw('LOWER(courses.status) LIKE ?', [$keyword])
                                        ->orWhereHas('instructor', function ($sub) use ($keyword) {
                                            $sub->whereRaw('LOWER(name) LIKE ?', [$keyword]);
                                        })
                                        ->orWhereHas('category', function ($sub) use ($keyword) {
                                            $sub->whereRaw('LOWER(name) LIKE ?', [$keyword]);
                                        });
                                });
                            }

                            return $query;
                        })
                );
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

                $startOfDay = \Carbon\Carbon::parse($value)->startOfDay();
                $endOfDay = \Carbon\Carbon::parse($value)->endOfDay();

                if ($key === 'start_date') {
                    return $query->whereBetween('start_date', [$startOfDay, $endOfDay]);
                }

                if ($key === 'end_date') {
                    return $query->whereBetween('end_date', [$startOfDay, $endOfDay]);
                }

                return false;
            });
    }
}
