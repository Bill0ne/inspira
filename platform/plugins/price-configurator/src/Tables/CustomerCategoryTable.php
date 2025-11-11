<?php

namespace Botble\PriceConfigurator\Tables;

use Botble\PriceConfigurator\Models\CustomerCategory;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\Actions\EditAction;
use Botble\Table\BulkActions\DeleteBulkAction;
use Botble\Table\BulkChanges\CreatedAtBulkChange;
use Botble\Table\BulkChanges\StatusBulkChange;
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\StatusColumn;
use Botble\Table\Columns\FormattedColumn;
use Botble\Table\HeaderActions\CreateHeaderAction;
use Illuminate\Database\Eloquent\Builder;

class CustomerCategoryTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(CustomerCategory::class)
            ->addHeaderAction(CreateHeaderAction::make()->route('customer-category.create'))
            ->addActions([
                EditAction::make()->route('customer-category.edit'),
                DeleteAction::make()->route('customer-category.destroy'),
            ])
            ->addColumns([
                IdColumn::make(),
                FormattedColumn::make('code')->title(trans('plugins/price-configurator::price-configurator.forms.code')),
                FormattedColumn::make('label')->title(trans('plugins/price-configurator::price-configurator.forms.label')),
                FormattedColumn::make('description')->title(trans('plugins/price-configurator::price-configurator.forms.description')),
                StatusColumn::make(),
                CreatedAtColumn::make(),
            ])
            ->addBulkActions([
                DeleteBulkAction::make()->permission('customer-category.destroy'),
            ])
            ->addBulkChanges([
                StatusBulkChange::make(),
                CreatedAtBulkChange::make(),
            ])
            ->queryUsing(fn(Builder $query) => $query->select([
                'id',
                'code',
                'label',
                'description',
                'status',
                'created_at'
            ]));
    }
}
