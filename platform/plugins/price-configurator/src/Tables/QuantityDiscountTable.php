<?php

namespace Botble\PriceConfigurator\Tables;

use Botble\PriceConfigurator\Models\QuantityDiscount;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\Actions\EditAction;
use Botble\Table\BulkActions\DeleteBulkAction;
use Botble\Table\BulkChanges\CreatedAtBulkChange;
use Botble\Table\BulkChanges\StatusBulkChange;
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\EnumColumn;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\StatusColumn;
use Botble\Table\Columns\FormattedColumn;
use Botble\Table\HeaderActions\CreateHeaderAction;
use Illuminate\Database\Eloquent\Builder;

class QuantityDiscountTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(QuantityDiscount::class)
            ->addHeaderAction(CreateHeaderAction::make()->route('quantity-discount.create'))
            ->addActions([
                EditAction::make()->route('quantity-discount.edit'),
                DeleteAction::make()->route('quantity-discount.destroy'),
            ])
            ->addColumns([
                IdColumn::make(),
                FormattedColumn::make('title')->title(trans('plugins/price-configurator::price-configurator.forms.title')),
                EnumColumn::make('condition_type')->title(trans('plugins/price-configurator::price-configurator.forms.condition_type')),
                FormattedColumn::make('range_min')->title(trans('plugins/price-configurator::price-configurator.forms.range_min'))->withEmptyState(),
                FormattedColumn::make('range_max')->title(trans('plugins/price-configurator::price-configurator.forms.range_max'))->withEmptyState(),
                EnumColumn::make('discount_type')->title(trans('plugins/price-configurator::price-configurator.forms.discount_type')),
                FormattedColumn::make('discount_value')->title(trans('plugins/price-configurator::price-configurator.forms.discount_value')),
                FormattedColumn::make('priority')->title(trans('plugins/price-configurator::price-configurator.forms.priority')),
                StatusColumn::make(),
                CreatedAtColumn::make(),
            ])
            ->addBulkActions([
                DeleteBulkAction::make()->permission('quantity-discount.destroy'),
            ])
            ->addBulkChanges([
                StatusBulkChange::make(),
                CreatedAtBulkChange::make(),
            ])
            ->queryUsing(fn(Builder $query) => $query->select([
                'id',
                'title',
                'condition_type',
                'range_min',
                'range_max',
                'discount_type',
                'discount_value',
                'priority',
                'status',
                'created_at',
            ]));
    }
}
