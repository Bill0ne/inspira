<?php

namespace Botble\Hotel\Tables;

use Botble\Base\Facades\Html;
use Botble\Hotel\Models\CustomerCard;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\Actions\EditAction;
use Botble\Table\BulkActions\DeleteBulkAction;
use Botble\Table\Columns\Column;
use Botble\Table\Columns\IdColumn;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class CustomerCardTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(CustomerCard::class)
            ->addActions([
                EditAction::make()->route('customer-cards.edit'),
                DeleteAction::make()->route('customer-cards.destroy'),
            ]);
    }

    public function ajax(): JsonResponse
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('type', fn (CustomerCard $card) => $card->type?->label() ?? '—')
            ->editColumn('base_price', fn (CustomerCard $card) => format_price($card->base_price))
            ->editColumn('discount_percent', fn (CustomerCard $card) => number_format($card->discount_percent, 2) . '%')
            ->editColumn('units_remaining', function (CustomerCard $card) {
                return sprintf('%d / %d', $card->units_remaining, $card->units_total);
            })
            ->editColumn('valid_until', function (CustomerCard $card) {
                if (! $card->valid_until) {
                    return '&mdash;';
                }

                if ($card->valid_until->isPast()) {
                    return Html::tag('span', $card->valid_until->toDateString(), ['class' => 'badge badge-danger']);
                }

                if ($card->valid_until->diffInDays(now()) <= 7) {
                    return Html::tag('span', $card->valid_until->toDateString(), ['class' => 'badge badge-warning']);
                }

                return Html::tag('span', $card->valid_until->toDateString(), ['class' => 'badge badge-success']);
            })
            ->editColumn('assigned_to', function (CustomerCard $card) {
                return $card->customer?->email ?: '—';
            })
            ->addColumn('status', function (CustomerCard $card) {
                $label = $card->status_label;
                $color = $card->status_color ?? $card->getStatusColorAttribute();

                return Html::tag('span', $label, ['class' => 'badge badge-' . $color]);
            });

        return $this->toJson($data);
    }

    public function query(): Relation|Builder|QueryBuilder
    {
        $query = $this->getModel()->query()->select(['*'])->with('customer');

        return $this->applyScopes($query);
    }

    public function columns(): array
    {
        return [
            IdColumn::make(),
            Column::make('name')->title(trans('plugins/hotel::customer-card.table.name'))->alignLeft(),
            Column::make('type')->title(trans('plugins/hotel::customer-card.table.type'))->alignLeft(),
            Column::make('discount_percent')->title(trans('plugins/hotel::customer-card.table.discount'))->alignLeft(),
            Column::make('base_price')->title(trans('plugins/hotel::customer-card.table.base_price'))->alignLeft(),
            Column::make('units_remaining')->title(trans('plugins/hotel::customer-card.table.units_remaining'))->alignLeft(),
            Column::make('valid_until')->title(trans('plugins/hotel::customer-card.table.valid_until'))->alignLeft(),
            Column::make('assigned_to')->title(trans('plugins/hotel::customer-card.table.assigned_to'))->alignLeft(),
            Column::make('status')->title(trans('plugins/hotel::customer-card.table.status'))->alignLeft(),
        ];
    }

    public function buttons(): array
    {
        return $this->addCreateButton(route('customer-cards.create'), 'customer-cards.create');
    }

    public function bulkActions(): array
    {
        return [
            DeleteBulkAction::make()->permission('customer-cards.destroy'),
        ];
    }

    public function renderTable($data = [], $mergeData = []): View|Factory|Response
    {
        if ($this->isEmpty()) {
            return view('plugins/hotel::customer-cards.intro');
        }

        return parent::renderTable($data, $mergeData);
    }
}
