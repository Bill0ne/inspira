<?php

namespace Botble\InspiraCancellation\Tables;

use Botble\InspiraCancellation\Models\Cancellation;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\FormattedColumn;
use Botble\Table\Columns\IdColumn;
use Illuminate\Database\Eloquent\Builder;

class CancellationTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(Cancellation::class)
            ->addColumns([
                IdColumn::make(),
                FormattedColumn::make('booking_reference')
                    ->title(trans('plugins/inspira-cancellation::cancellation.cancellation.booking'))
                    ->alignStart()
                    ->getValueUsing(fn (FormattedColumn $column) => $column->getItem()->booking_reference ?? $column->getItem()->booking_id),
                FormattedColumn::make('booking_type')
                    ->title(trans('plugins/inspira-cancellation::cancellation.email.variables.booking_type'))
                    ->alignStart()
                    ->getValueUsing(function (FormattedColumn $column) {
                        return match ($column->getItem()->booking_type) {
                            'course' => trans('plugins/courses::courses.course.name'),
                            'room' => trans('plugins/hotel::booking.room'),
                            default => $column->getItem()->booking_type,
                        };
                    }),
                FormattedColumn::make('refund_amount')
                    ->title(trans('plugins/inspira-cancellation::cancellation.cancellation.refund'))
                    ->alignEnd()
                    ->getValueUsing(fn (FormattedColumn $column) => format_price($column->getItem()->refund_amount)),
                FormattedColumn::make('refund_percent')
                    ->title(trans('plugins/inspira-cancellation::cancellation.rule.refund_percent'))
                    ->alignCenter()
                    ->getValueUsing(fn (FormattedColumn $column) => $column->getItem()->refund_percent . '%'),
                FormattedColumn::make('rule_description')
                    ->title(trans('plugins/inspira-cancellation::cancellation.rule.description'))
                    ->alignStart()
                    ->getValueUsing(fn (FormattedColumn $column) => $column->getItem()->rule?->description),
                FormattedColumn::make('status')
                    ->title(trans('plugins/inspira-cancellation::cancellation.cancellation.status'))
                    ->alignStart(),
                CreatedAtColumn::make()
                    ->title(trans('plugins/inspira-cancellation::cancellation.cancellation.created_at')),
            ])
            ->queryUsing(function (Builder $query) {
                return $query->with('rule');
            })
            ->disableActions()
            ->disableBulkActions();
    }
}
