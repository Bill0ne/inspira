<?php

namespace Botble\InspiraCancellation\Tables;

use Botble\InspiraCancellation\Enums\CancellationStatusEnum;
use Botble\InspiraCancellation\Models\Cancellation;
use Botble\InspiraCancellation\Tables\Actions\ConditionalAction;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\FormattedColumn;
use Botble\Table\Columns\IdColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;

class CancellationTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(Cancellation::class)
            ->addActions([
                ConditionalAction::make('approve')
                    ->label(trans('plugins/inspira-cancellation::cancellation.actions.approve'))
                    ->icon('ti ti-check')
                    ->color('success')
                    ->action('POST')
                    ->route('inspira-cancellation.cancellations.approve')
                    ->permission('inspira-cancellation.cancellations.manage')
                    ->confirmation()
                    ->confirmationModalTitle(trans('plugins/inspira-cancellation::cancellation.actions.approve_title'))
                    ->confirmationModalMessage(trans('plugins/inspira-cancellation::cancellation.actions.approve_confirm'))
                    ->confirmationModalButton(trans('plugins/inspira-cancellation::cancellation.actions.approve'))
                    ->displayIf(fn (ConditionalAction $action) => $this->shouldShowApproval($action)),
                ConditionalAction::make('mark-paid')
                    ->label(trans('plugins/inspira-cancellation::cancellation.actions.mark_paid'))
                    ->icon('ti ti-cash')
                    ->color('primary')
                    ->action('POST')
                    ->route('inspira-cancellation.cancellations.mark-paid')
                    ->permission('inspira-cancellation.cancellations.manage')
                    ->confirmation()
                    ->confirmationModalTitle(trans('plugins/inspira-cancellation::cancellation.actions.mark_paid_title'))
                    ->confirmationModalMessage(trans('plugins/inspira-cancellation::cancellation.actions.mark_paid_confirm'))
                    ->confirmationModalButton(trans('plugins/inspira-cancellation::cancellation.actions.mark_paid'))
                    ->displayIf(fn (ConditionalAction $action) => $this->shouldShowMarkPaid($action)),
                ConditionalAction::make('reject')
                    ->label(trans('plugins/inspira-cancellation::cancellation.actions.reject'))
                    ->icon('ti ti-x')
                    ->color('danger')
                    ->action('POST')
                    ->route('inspira-cancellation.cancellations.reject')
                    ->permission('inspira-cancellation.cancellations.manage')
                    ->confirmation()
                    ->confirmationModalTitle(trans('plugins/inspira-cancellation::cancellation.actions.reject_title'))
                    ->confirmationModalMessage(trans('plugins/inspira-cancellation::cancellation.actions.reject_confirm'))
                    ->confirmationModalButton(trans('plugins/inspira-cancellation::cancellation.actions.reject'))
                    ->displayIf(fn (ConditionalAction $action) => $this->shouldShowRejection($action)),
            ])
            ->displayActionsAsDropdownWhenActionsMoresThan(4)
            ->addColumns([
                IdColumn::make(),
                FormattedColumn::make('booking_reference')
                    ->title(trans('plugins/inspira-cancellation::cancellation.cancellation.booking'))
                    ->alignStart()
                    ->getValueUsing(fn (FormattedColumn $column, $value) => $column->getItem()->booking_reference ?? $column->getItem()->booking_id ?? $value),
                FormattedColumn::make('booking_type')
                    ->title(trans('plugins/inspira-cancellation::cancellation.email.variables.booking_type'))
                    ->alignStart()
                    ->getValueUsing(function (FormattedColumn $column, $value) {
                        return match ($column->getItem()->booking_type) {
                            'course' => trans('plugins/courses::courses.course.name'),
                            'room' => trans('plugins/hotel::booking.room'),
                            default => $column->getItem()->booking_type ?? $value,
                        };
                    }),
                FormattedColumn::make('refund_amount')
                    ->title(trans('plugins/inspira-cancellation::cancellation.cancellation.refund'))
                    ->alignEnd()
                    ->getValueUsing(fn (FormattedColumn $column, $value) => format_price($column->getItem()->refund_amount ?? $value)),
                FormattedColumn::make('refund_percent')
                    ->title(trans('plugins/inspira-cancellation::cancellation.rule.refund_percent'))
                    ->alignCenter()
                    ->getValueUsing(function (FormattedColumn $column, $value) {
                        $percent = $column->getItem()->refund_percent ?? $value;

                        return is_null($percent) ? $value : $percent . '%';
                    }),
                FormattedColumn::make('rule_description')
                    ->title(trans('plugins/inspira-cancellation::cancellation.rule.description'))
                    ->alignStart()
                    ->getValueUsing(fn (FormattedColumn $column, $value) => $column->getItem()->rule?->description ?? $value),
                FormattedColumn::make('status')
                    ->title(trans('plugins/inspira-cancellation::cancellation.cancellation.status'))
                    ->alignCenter()
                    ->getValueUsing(function (FormattedColumn $column, $value) {
                        $status = $column->getItem()->status;

                        if ($status instanceof CancellationStatusEnum) {
                            return $status->toHtml();
                        }

                        return $value;
                    }),
                FormattedColumn::make('approved_at')
                    ->title(trans('plugins/inspira-cancellation::cancellation.cancellation.approved_at'))
                    ->alignCenter()
                    ->getValueUsing(fn (FormattedColumn $column, $value) => optional($column->getItem()->approved_at)->format('d.m.Y H:i')), 
                FormattedColumn::make('refunded_at')
                    ->title(trans('plugins/inspira-cancellation::cancellation.cancellation.refunded_at'))
                    ->alignCenter()
                    ->getValueUsing(fn (FormattedColumn $column, $value) => optional($column->getItem()->refunded_at)->format('d.m.Y H:i')),
                CreatedAtColumn::make()
                    ->title(trans('plugins/inspira-cancellation::cancellation.cancellation.created_at')),
            ])
            ->queryUsing(function (Builder $query) {
                return $query
                    ->select(['insp_cancellations.*'])
                    ->with(['rule', 'customer', 'approver', 'refunder']);
            })
            ->removeAllBulkActions();
    }

    public function ajax(): JsonResponse
    {
        return $this->toJson(
            $this->table->eloquent($this->query())
        );
    }

    protected function statusValue(ConditionalAction $action): string
    {
        $status = $action->getItem()->status;

        if ($status instanceof CancellationStatusEnum) {
            return $status->getValue();
        }

        return (string) $status;
    }

    protected function shouldShowApproval(ConditionalAction $action): bool
    {
        return in_array($this->statusValue($action), [
            CancellationStatusEnum::PENDING,
            CancellationStatusEnum::PENDING_REFUND,
        ], true);
    }

    protected function shouldShowMarkPaid(ConditionalAction $action): bool
    {
        return in_array($this->statusValue($action), [
            CancellationStatusEnum::APPROVED,
        ], true);
    }

    protected function shouldShowRejection(ConditionalAction $action): bool
    {
        return in_array($this->statusValue($action), [
            CancellationStatusEnum::PENDING,
            CancellationStatusEnum::PENDING_REFUND,
        ], true);
    }
}
