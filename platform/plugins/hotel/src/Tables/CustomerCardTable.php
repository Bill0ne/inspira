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
    $service = app(CustomerCardService::class);

    $data = $this->table
        ->eloquent($this->query())
        ->editColumn('type', fn (CustomerCard $card) => $card->type?->label() ?? '—')
        ->editColumn('base_price', fn (CustomerCard $card) => format_price($card->base_price))
        ->editColumn('discount_percent', fn (CustomerCard $card) => number_format($card->discount_percent, 2) . '%')
        ->editColumn('name', function (CustomerCard $card) use ($service) {
            $uidBadge = $card->uid
                ? Html::tag('span', e($card->uid), ['class' => 'badge bg-success ms-2'])
                : '';

            $meta = $card->assigned_to
                ? trans('plugins/hotel::customer-card.table.assignment_meta', [
                    'remaining' => number_format((float) $card->units_remaining, 0),
                    'total' => number_format((float) $card->units_total, 0),
                ])
                : trans('plugins/hotel::customer-card.table.template_meta', [
                    'units' => number_format((float) $card->units_total, 0),
                    'price' => format_price($service->calculatePurchasePrice($card)),
                ]);

            return Html::tag(
                'div',
                Html::tag('div', e($card->name) . $uidBadge, ['class' => 'fw-semibold text-success mb-1'])
                . Html::tag('div', e($meta), ['class' => 'text-muted small mb-0']),
                ['class' => 'bg-light rounded-3 p-3']
            );
        });

    // ADD MODE-SPECIFIC COLUMNS (assigned / template)
    if ($this->assignedOnly) {

        // Assigned mode columns
        $data->addColumn('units_remaining', function (CustomerCard $card) {
            return sprintf('%d / %d', $card->units_remaining, $card->units_total);
        });

        $data->addColumn('assigned_to', function (CustomerCard $card) {
            $customer = $card->customer;

            if (! $customer) {
                return '&mdash;';
            }

            $name = e($customer->name ?: $customer->email ?: '—');
            $email = $customer->email
                ? Html::tag('div', e($customer->email), ['class' => 'text-muted'])
                : '';

            return Html::tag('div', $name . $email, ['class' => 'lh-sm']);
        });

    } else {

        // Template mode columns
        $data->addColumn('purchase_price', function (CustomerCard $card) use ($service) {
            return format_price($service->calculatePurchasePrice($card));
        });

        $data->addColumn('active_assignments', function (CustomerCard $card) {
            return sprintf('%d', (int) $card->active_assignments_count);
        });
    }

    // VALID UNTIL COLUMN
    $data->editColumn('valid_until', function (CustomerCard $card) {
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
    });

    if ($this->assignedOnly) {
        // USAGE BUTTON
        $data->addColumn('usage', function (CustomerCard $card) {
            return Html::tag(
                'button',
                trans('plugins/hotel::customer-card.table.view_usage'),
                [
                    'class' => 'btn btn-outline-primary btn-sm',
                    'type' => 'button',
                    'data-bb-customer-card' => 'usage',
                    'data-card-id' => $card->getKey(),
                    'data-title' => $card->name,
                    'data-url' => route('customer-cards.usages', $card),
                ]
            );
        });
    }

    // STATUS BADGE
    $data->addColumn('status', function (CustomerCard $card) {
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
        ];

        if ($this->assignedOnly) {
            $columns[] = Column::make('assigned_to')
                ->title(trans('plugins/hotel::customer-card.table.assigned_to'))
                ->alignLeft();

            $columns[] = Column::make('units_remaining')
                ->title(trans('plugins/hotel::customer-card.table.units_remaining'))
                ->alignLeft();
        } else {
            $columns[] = Column::make('purchase_price')
                ->title(trans('plugins/hotel::customer-card.table.purchase_price'))
                ->alignLeft();

            $columns[] = Column::make('active_assignments')
                ->title(trans('plugins/hotel::customer-card.table.active_assignments'))
                ->alignLeft();
        }

        $columns[] = Column::make('valid_until')
            ->title(trans('plugins/hotel::customer-card.table.valid_until'))
            ->alignLeft();

        $columns[] = Column::make('status')
            ->title(trans('plugins/hotel::customer-card.table.status'))
            ->alignLeft();

        if ($this->assignedOnly) {
            $columns[] = Column::make('usage')
                ->title(trans('plugins/hotel::customer-card.table.view_usage'))
                ->alignLeft();
        }

        return $columns;
    }

    public function buttons(): array
    {
        $route = $this->assignedOnly
            ? route('customer-cards.create', ['assigned' => 1])
            : route('customer-cards.create');

        return $this->addCreateButton($route, 'customer-cards.create');
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
