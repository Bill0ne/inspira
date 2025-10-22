<?php

namespace Botble\PriceConfigurator\Tables;

use Botble\PriceConfigurator\Models\Tier;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\Actions\EditAction;
use Botble\Table\BulkActions\DeleteBulkAction;
use Botble\Table\BulkChanges\CreatedAtBulkChange;
use Botble\Table\BulkChanges\NameBulkChange;
use Botble\Table\BulkChanges\StatusBulkChange;
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\NameColumn;
use Botble\Table\Columns\StatusColumn;
use Botble\Table\Columns\FormattedColumn;
use Botble\Table\HeaderActions\CreateHeaderAction;
use Illuminate\Database\Eloquent\Builder;

class TierTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(Tier::class)
            ->addHeaderAction(CreateHeaderAction::make()->route('tier.create'))
            ->addActions([
                EditAction::make()->route('tier.edit'),
                DeleteAction::make()->route('tier.destroy'),
            ])
            ->addColumns([
                IdColumn::make(),
                NameColumn::make()->route('tier.edit'),
                FormattedColumn::make('priority')->title(__('Priority')),
                FormattedColumn::make('is_exclusive')
                    ->title(__('Exclusive'))
                    ->getValueUsing(function ($col) {
                        $isExclusive = $col->getItem()->is_exclusive;

                        return sprintf(
                            '<span class="badge %s">%s</span>',
                            $isExclusive ? 'badge bg-primary text-white' : 'badge bg-warning text-white',
                            $isExclusive ? __('Yes') : __('No')
                        );
                    }),
                FormattedColumn::make('starts_at')->title(__('Starts At'))->withEmptyState(),
                FormattedColumn::make('ends_at')->title(__('Ends At'))->withEmptyState(),
                FormattedColumn::make('notes')->title(__('Notes')),
                StatusColumn::make(),
                CreatedAtColumn::make(),
            ])
            ->addBulkActions([
                DeleteBulkAction::make()->permission('tier.destroy'),
            ])
            ->addBulkChanges([
                NameBulkChange::make(),
                StatusBulkChange::make(),
                CreatedAtBulkChange::make(),
            ])
            ->queryUsing(fn(Builder $query) => $query->select([
                'id',
                'name',
                'priority',
                'is_exclusive',
                'starts_at',
                'ends_at',
                'notes',
                'status',
                'created_at'
            ]));
    }
}
