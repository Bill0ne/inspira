<?php

namespace Botble\Hotel\Tables;

use Botble\Base\Facades\Html;
use Botble\Hotel\Models\CustomerCard;
use Botble\Hotel\Services\CustomerCardService;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\Actions\EditAction;
use Botble\Table\BulkActions\DeleteBulkAction;
use Botble\Table\Columns\Column;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Supports\Builder as TableBuilder;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class CustomerCardTable extends TableAbstract
{
    protected bool $assignedOnly = false;

    public function setup(): void
    {
        $this
            ->model(CustomerCard::class)
            ->addActions([
                EditAction::make()->route('customer-cards.edit'),
                DeleteAction::make()->route('customer-cards.destroy'),
            ]);
    }

    public function showAssignedOnly(bool $assignedOnly = true): static
    {
        $this->assignedOnly = $assignedOnly;

        return $this;
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
            ->addColumn('purchase_price', function (CustomerCard $card) {
                $price = app(CustomerCardService::class)->calculatePurchasePrice($card);

                return format_price($price);
            })
            ->addColumn('active_assignments', function (CustomerCard $card) {
                return sprintf('%d', (int) $card->active_assignments_count);
            })
            ->editColumn('name', function (CustomerCard $card) {
                $uidBadge = $card->uid
                    ? Html::tag('span', e($card->uid), ['class' => 'badge bg-success ms-2'])
                    : '';

                $meta = trans('plugins/hotel::customer-card.table.units_remaining') . ': ' . sprintf('%d / %d', $card->units_remaining, $card->units_total);

                return Html::tag('div',
                    Html::tag('div', e($card->name) . $uidBadge, [
                        'style' => 'font-weight:600;color:#1e7d6d;margin-bottom:4px;',
                    ]) .
                    Html::tag('div', e($meta), [
                        'style' => 'font-size:12px;color:#4b5c58;',
                    ]),
                    [
                        'style' => 'background:#f3f8f7;border-radius:12px;padding:12px 16px;',
                    ]
                );
            })
            ->when($this->assignedOnly, function ($dataTable) {
                return $dataTable->addColumn('assigned_to', function (CustomerCard $card) {
                    $customer = $card->customer;

                    if (! $customer) {
                        return '&mdash;';
                    }

                    $name = e($customer->name ?: $customer->email ?: '—');
                    $email = $customer->email
                        ? Html::tag('div', e($customer->email), ['class' => 'text-muted'])
                        : '';

                    return Html::tag('div', $name . $email, [
                        'style' => 'line-height:1.35;',
                    ]);
                });
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
            ->addColumn('usage', function (CustomerCard $card) {
                return Html::tag('button', trans('plugins/hotel::customer-card.table.view_usage'), [
                    'class' => 'btn btn-outline-primary btn-sm',
                    'type' => 'button',
                    'data-bb-customer-card' => 'usage',
                    'data-card-id' => $card->getKey(),
                    'data-title' => $card->name,
                    'data-url' => route('customer-cards.usages', $card),
                ]);
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
        $query = $this->getModel()->query()
            ->select(['ht_customer_cards.*']);

        if ($this->assignedOnly) {
            $query->whereNotNull('assigned_to')->with(['customer']);
        } else {
            $query->whereNull('assigned_to');
        }

        $query
            ->withCount([
                'orders as active_assignments_count' => function ($relation) {
                    $relation
                        ->where('status', 'completed')
                        ->whereHas('assignedCard', function ($assignedCard) {
                            $assignedCard->active();
                        });
                },
            ]);

        return $this->applyScopes($query);
    }

    public function columns(): array
    {
        $columns = [
            IdColumn::make(),
            Column::make('name')->title(trans('plugins/hotel::customer-card.table.name'))->alignLeft(),
            Column::make('type')->title(trans('plugins/hotel::customer-card.table.type'))->alignLeft(),
            Column::make('discount_percent')->title(trans('plugins/hotel::customer-card.table.discount'))->alignLeft(),
            Column::make('base_price')->title(trans('plugins/hotel::customer-card.table.base_price'))->alignLeft(),
            Column::make('purchase_price')->title(trans('plugins/hotel::customer-card.table.purchase_price'))->alignLeft(),
        ];

        if ($this->assignedOnly) {
            $columns[] = Column::make('assigned_to')
                ->title(trans('plugins/hotel::customer-card.table.assigned_to'))
                ->alignLeft();
        }

        $columns = array_merge($columns, [
            Column::make('units_remaining')->title(trans('plugins/hotel::customer-card.table.units_remaining'))->alignLeft(),
            Column::make('active_assignments')->title(trans('plugins/hotel::customer-card.table.active_assignments'))->alignLeft(),
            Column::make('valid_until')->title(trans('plugins/hotel::customer-card.table.valid_until'))->alignLeft(),
            Column::make('status')->title(trans('plugins/hotel::customer-card.table.status'))->alignLeft(),
            Column::make('usage')->title(trans('plugins/hotel::customer-card.table.view_usage'))->alignLeft(),
        ]);

        return $columns;
    }

    public function buttons(): array
    {
        return $this->addCreateButton(route('customer-cards.create'), 'customer-cards.create');
    }

    public function html(): TableBuilder
    {
        return parent::html()->ajax([
            'url' => $this->getAjaxUrl(),
            'method' => 'GET',
        ]);
    }

    public function bulkActions(): array
    {
        return [
            DeleteBulkAction::make()->permission('customer-cards.destroy'),
        ];
    }

    public function renderTable($data = [], $mergeData = []): View|Factory|Response
    {
        if (! $this->assignedOnly && $this->isEmpty()) {
            return view('plugins/hotel::customer-cards.intro');
        }

        return parent::renderTable($data, $mergeData);
    }
}
