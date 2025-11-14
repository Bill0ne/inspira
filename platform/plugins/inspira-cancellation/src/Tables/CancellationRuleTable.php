<?php

namespace Botble\InspiraCancellation\Tables;

use Botble\InspiraCancellation\Models\CancellationRule;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\Actions\EditAction;
use Botble\Table\BulkActions\DeleteBulkAction;
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\FormattedColumn;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\StatusColumn;
use Botble\Table\HeaderActions\CreateHeaderAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class CancellationRuleTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(CancellationRule::class)
            ->addColumns([
                IdColumn::make(),
                FormattedColumn::make('type')
                    ->title(trans('plugins/inspira-cancellation::cancellation.rule.type'))
                    ->alignStart()
                    ->getValueUsing(function (FormattedColumn $column, $value) {
                        return match ($column->getItem()->type) {
                            'course' => trans('plugins/courses::courses.course.name'),
                            'room' => trans('plugins/hotel::booking.room'),
                            default => $column->getItem()->type ?? $value,
                        };
                    }),
                FormattedColumn::make('range_display')
                    ->title(trans('plugins/inspira-cancellation::cancellation.rule.from_days'))
                    ->alignStart()
                    ->getValueUsing(function (FormattedColumn $column, $value) {
                        $item = $column->getItem();
                        $from = is_null($item->from_days) ? '0' : $item->from_days;
                        $to = is_null($item->to_days) ? '∞' : $item->to_days;

                        $range = sprintf('%s – %s', $from, $to);

                        return $range !== '' ? $range : $value;
                    }),
                FormattedColumn::make('refund_percent')
                    ->title(trans('plugins/inspira-cancellation::cancellation.rule.refund_percent'))
                    ->alignCenter()
                    ->getValueUsing(function (FormattedColumn $column, $value) {
                        $percent = $column->getItem()->refund_percent ?? $value;

                        return is_null($percent) ? $value : $percent . '%';
                    }),
                StatusColumn::make('active')
                    ->title(trans('plugins/inspira-cancellation::cancellation.rule.active')),
                CreatedAtColumn::make(),
            ])
            ->addHeaderAction(
                CreateHeaderAction::make()
                    ->route('inspira-cancellation.rules.create')
            )
            ->addActions([
                EditAction::make()->route('inspira-cancellation.rules.edit'),
                DeleteAction::make()->route('inspira-cancellation.rules.destroy'),
            ])
            ->addBulkAction(DeleteBulkAction::make()->permission('inspira-cancellation.rules.destroy'))
            ->queryUsing(function (Builder $query) {
                return $query
                    ->select(['id', 'type', 'from_days', 'to_days', 'refund_percent', 'active', 'created_at'])
                    ->addSelect(DB::raw('NULL as range_display'));
            });
    }
}
