<?php

namespace Botble\PriceConfigurator\Tables;

use Botble\PriceConfigurator\Models\Rule;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\Actions\EditAction;
use Botble\Table\BulkActions\DeleteBulkAction;
use Botble\Table\BulkChanges\CreatedAtBulkChange;
use Botble\Table\BulkChanges\StatusBulkChange;
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\EnumColumn;
use Botble\Table\Columns\FormattedColumn;
use Botble\Table\Columns\StatusColumn;
use Botble\Table\HeaderActions\CreateHeaderAction;
use Illuminate\Database\Eloquent\Builder;

class RuleTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(Rule::class)
            ->addHeaderAction(CreateHeaderAction::make()->route('rule.create'))
            ->addActions([
                EditAction::make()->route('rule.edit'),
                DeleteAction::make()->route('rule.destroy'),
            ])
            ->addColumns([
                IdColumn::make(),
                FormattedColumn::make('tier_id')
                    ->title(trans('plugins/price-configurator::price-configurator.tier.name'))
                    ->getValueUsing(fn($col) => $col->getItem()->tier?->name ?? '—'),
                FormattedColumn::make('customer_category_id')
                    ->title(trans('plugins/price-configurator::price-configurator.forms.customer_category'))
                    ->getValueUsing(fn($col) => $col->getItem()->customerCategory
                        ? $col->getItem()->customerCategory->code . ' - ' . $col->getItem()->customerCategory->label
                        : '—'
                    ),
                EnumColumn::make('scope')->title(trans('plugins/price-configurator::price-configurator.forms.scope')),
                EnumColumn::make('calculation_type')->title(trans('plugins/price-configurator::price-configurator.forms.calculation_type')),
                FormattedColumn::make('calculation_value')->title(trans('plugins/price-configurator::price-configurator.forms.calculation_value')),
                EnumColumn::make('rounding_mode')->title(trans('plugins/price-configurator::price-configurator.forms.rounding_mode')),
                FormattedColumn::make('round_to')->title(trans('plugins/price-configurator::price-configurator.forms.round_to')),
                StatusColumn::make(),
                CreatedAtColumn::make(),
            ])
            ->addBulkActions([
                DeleteBulkAction::make()->permission('rule.destroy'),
            ])
            ->addBulkChanges([
                StatusBulkChange::make(),
                CreatedAtBulkChange::make(),
            ])
            ->queryUsing(fn(Builder $query) => $query
                ->with(['tier:id,name', 'customerCategory:id,label,code'])
                ->select([
                    'id',
                    'price_tier_id',
                    'customer_category_id',
                    'scope',
                    'calculation_type',
                    'calculation_value',
                    'rounding_mode',
                    'round_to',
                    'status',
                    'created_at',
                ]));
    }
}
