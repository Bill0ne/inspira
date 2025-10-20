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
                FormattedColumn::make('title')->title(__('Title')),
                EnumColumn::make('condition_type')->title(__('Condition Type')),
                FormattedColumn::make('range_min')->title(__('Range Min'))->withEmptyState(),
                FormattedColumn::make('range_max')->title(__('Range Max'))->withEmptyState(),
                EnumColumn::make('discount_type')->title(__('Discount Type')),
                FormattedColumn::make('discount_value')->title(__('Discount Value')),
                FormattedColumn::make('priority')->title(__('Priority')),
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
