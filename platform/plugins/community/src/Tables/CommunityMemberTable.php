<?php

namespace Botble\Community\Tables;

use Botble\Community\Models\CommunityMember;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\Actions\EditAction;
use Botble\Table\BulkActions\DeleteBulkAction;
use Botble\Table\BulkChanges\CreatedAtBulkChange;
use Botble\Table\BulkChanges\NameBulkChange;
use Botble\Table\BulkChanges\StatusBulkChange;
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\FormattedColumn;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\NameColumn;
use Botble\Table\Columns\StatusColumn;
use Botble\Table\HeaderActions\CreateHeaderAction;
use Illuminate\Database\Eloquent\Builder;

class CommunityMemberTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(CommunityMember::class)
            ->addHeaderAction(CreateHeaderAction::make()->route('community.create'))
            ->addActions([
                EditAction::make()->route('community.edit'),
                DeleteAction::make()->route('community.destroy'),
            ])
            ->addColumns([
                IdColumn::make(),
                NameColumn::make()->route('community.edit'),
                FormattedColumn::make('customer_id')
                    ->title(trans('plugins/community::community.table.user'))
                    ->getValueUsing(function (FormattedColumn $column) {
                        $customer = $column->getItem()->customer;

                        if (! $customer || ! $customer->getKey()) {
                            return '—';
                        }

                        return $customer->name . ' (' . $customer->email . ')';
                    }),
                CreatedAtColumn::make(),
                StatusColumn::make(),
            ])
            ->addBulkActions([
                DeleteBulkAction::make()->permission('community.destroy'),
            ])
            ->addBulkChanges([
                NameBulkChange::make(),
                StatusBulkChange::make(),
                CreatedAtBulkChange::make(),
            ])
            ->queryUsing(function (Builder $query) {
                $query
                    ->with(['customer:id,first_name,last_name,email'])
                    ->select([
                        'id',
                        'name',
                        'customer_id',
                        'created_at',
                        'status',
                    ]);
            });
    }
}
