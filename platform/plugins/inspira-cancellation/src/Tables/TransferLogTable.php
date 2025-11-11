<?php

namespace Botble\InspiraCancellation\Tables;

use Botble\InspiraCancellation\Models\TransferLog;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\FormattedColumn;
use Botble\Table\Columns\IdColumn;
use Illuminate\Database\Eloquent\Builder;

class TransferLogTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(TransferLog::class)
            ->addColumns([
                IdColumn::make(),
                FormattedColumn::make('booking_reference')
                    ->title(trans('plugins/inspira-cancellation::cancellation.cancellation.booking'))
                    ->getValueUsing(function (FormattedColumn $column) {
                        return $column->getItem()->booking_id;
                    }),
                FormattedColumn::make('booking_type')
                    ->title(trans('plugins/inspira-cancellation::cancellation.email.variables.booking_type'))
                    ->getValueUsing(function (FormattedColumn $column) {
                        return match ($column->getItem()->booking_type) {
                            'course' => trans('plugins/courses::courses.course.name'),
                            'room' => trans('plugins/hotel::booking.room'),
                            default => $column->getItem()->booking_type,
                        };
                    }),
                FormattedColumn::make('old_customer')
                    ->title(trans('plugins/inspira-cancellation::cancellation.transfer.old_customer'))
                    ->getValueUsing(function (FormattedColumn $column) {
                        $customer = $column->getItem()->oldCustomer;

                        return $customer?->email;
                    }),
                FormattedColumn::make('new_customer')
                    ->title(trans('plugins/inspira-cancellation::cancellation.transfer.new_customer'))
                    ->getValueUsing(function (FormattedColumn $column) {
                        $customer = $column->getItem()->newCustomer;

                        return $customer?->email;
                    }),
                CreatedAtColumn::make()
                    ->title(trans('plugins/inspira-cancellation::cancellation.transfer.created_at')),
            ])
            ->queryUsing(function (Builder $query) {
                return $query->with(['oldCustomer', 'newCustomer']);
            })
            ->disableActions()
            ->disableBulkActions();
    }
}
