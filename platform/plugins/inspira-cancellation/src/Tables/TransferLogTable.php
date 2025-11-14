<?php

namespace Botble\InspiraCancellation\Tables;

use Botble\InspiraCancellation\Enums\TransferStatusEnum;
use Botble\InspiraCancellation\Models\TransferLog;
use Botble\InspiraCancellation\Tables\Actions\ConditionalAction;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\FormattedColumn;
use Botble\Table\Columns\IdColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;

class TransferLogTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(TransferLog::class)
            ->addActions([
                ConditionalAction::make('transfer-approve')
                    ->label(trans('plugins/inspira-cancellation::cancellation.transfer.actions.approve'))
                    ->icon('ti ti-user-check')
                    ->color('success')
                    ->action('POST')
                    ->route('inspira-cancellation.transfers.approve')
                    ->permission('inspira-cancellation.transfers.manage')
                    ->confirmation()
                    ->confirmationModalTitle(trans('plugins/inspira-cancellation::cancellation.transfer.actions.approve_title'))
                    ->confirmationModalMessage(trans('plugins/inspira-cancellation::cancellation.transfer.actions.approve_confirm'))
                    ->confirmationModalButton(trans('plugins/inspira-cancellation::cancellation.transfer.actions.approve'))
                    ->displayIf(fn (ConditionalAction $action) => $this->isPending($action)),
                ConditionalAction::make('transfer-reject')
                    ->label(trans('plugins/inspira-cancellation::cancellation.transfer.actions.reject'))
                    ->icon('ti ti-user-x')
                    ->color('danger')
                    ->action('POST')
                    ->route('inspira-cancellation.transfers.reject')
                    ->permission('inspira-cancellation.transfers.manage')
                    ->confirmation()
                    ->confirmationModalTitle(trans('plugins/inspira-cancellation::cancellation.transfer.actions.reject_title'))
                    ->confirmationModalMessage(trans('plugins/inspira-cancellation::cancellation.transfer.actions.reject_confirm'))
                    ->confirmationModalButton(trans('plugins/inspira-cancellation::cancellation.transfer.actions.reject'))
                    ->displayIf(fn (ConditionalAction $action) => $this->isPending($action)),
            ])
            ->addColumns([
                IdColumn::make(),
                FormattedColumn::make('booking_reference')
                    ->title(trans('plugins/inspira-cancellation::cancellation.cancellation.booking'))
                    ->getValueUsing(fn (FormattedColumn $column, $value) => $column->getItem()->booking_id ?? $value),
                FormattedColumn::make('booking_type')
                    ->title(trans('plugins/inspira-cancellation::cancellation.email.variables.booking_type'))
                    ->getValueUsing(function (FormattedColumn $column, $value) {
                        return match ($column->getItem()->booking_type) {
                            'course' => trans('plugins/courses::courses.course.name'),
                            'room' => trans('plugins/hotel::booking.room'),
                            default => $column->getItem()->booking_type ?? $value,
                        };
                    }),
                FormattedColumn::make('old_customer')
                    ->title(trans('plugins/inspira-cancellation::cancellation.transfer.old_customer'))
                    ->getValueUsing(function (FormattedColumn $column, $value) {
                        $customer = $column->getItem()->oldCustomer;

                        return $customer?->email ?? $value;
                    }),
                FormattedColumn::make('new_customer')
                    ->title(trans('plugins/inspira-cancellation::cancellation.transfer.new_customer'))
                    ->getValueUsing(function (FormattedColumn $column, $value) {
                        $customer = $column->getItem()->newCustomer;

                        return $customer?->email ?? $value;
                    }),
                FormattedColumn::make('requested_replacement')
                    ->title(trans('plugins/inspira-cancellation::cancellation.transfer.requested_replacement'))
                    ->alignStart()
                    ->getValueUsing(function (FormattedColumn $column, $value) {
                        $payload = $column->getItem()->payload ?? [];

                        $name = trim(implode(' ', array_filter([
                            $payload['first_name'] ?? null,
                            $payload['last_name'] ?? null,
                        ])));

                        $email = $payload['email'] ?? null;

                        return collect([$name ?: null, $email])->filter()->implode(' • ');
                    }),
                FormattedColumn::make('status')
                    ->title(trans('plugins/inspira-cancellation::cancellation.transfer.status'))
                    ->alignCenter()
                    ->getValueUsing(function (FormattedColumn $column, $value) {
                        $status = $column->getItem()->status;

                        if ($status instanceof TransferStatusEnum) {
                            return $status->toHtml();
                        }

                        return $value;
                    }),
                FormattedColumn::make('approved_at')
                    ->title(trans('plugins/inspira-cancellation::cancellation.transfer.approved_at'))
                    ->alignCenter()
                    ->getValueUsing(fn (FormattedColumn $column, $value) => optional($column->getItem()->approved_at)->format('d.m.Y H:i')),
                CreatedAtColumn::make()
                    ->title(trans('plugins/inspira-cancellation::cancellation.transfer.created_at')),
            ])
            ->queryUsing(function (Builder $query) {
                return $query
                    ->select([
                        'insp_transfer_logs.*',
                    ])
                    ->with(['oldCustomer', 'newCustomer', 'requester', 'approver']);
            })
            ->removeAllBulkActions();
    }

    public function ajax(): JsonResponse
    {
        return $this->toJson(
            $this->table->eloquent($this->query())
        );
    }

    protected function isPending(ConditionalAction $action): bool
    {
        $status = $action->getItem()->status;

        if ($status instanceof TransferStatusEnum) {
            return $status->equals(TransferStatusEnum::PENDING());
        }

        return (string) $status === TransferStatusEnum::PENDING;
    }
}
