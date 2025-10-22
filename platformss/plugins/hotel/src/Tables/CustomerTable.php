<?php

namespace Botble\Hotel\Tables;

use Botble\Hotel\Models\Customer;
use Botble\PriceConfigurator\Models\CustomerCategory;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\Actions\EditAction;
use Botble\Table\BulkActions\DeleteBulkAction;
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\EmailColumn;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\NameColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class CustomerTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(Customer::class)
            ->addActions([
                EditAction::make()->route('customer.edit'),
                DeleteAction::make()->route('customer.destroy'),
            ]);
    }

    public function ajax(): JsonResponse
    {
        $data = $this->table
            ->eloquent($this->query())
            ->filter(function ($query) {
                $keyword = $this->request->input('search.value');

                if ($keyword) {
                    $kw = '%' . $keyword . '%';

                    return $query
                        ->where('ht_customers.first_name', 'LIKE', $kw)
                        ->orWhere('ht_customers.last_name', 'LIKE', $kw)
                        ->orWhere(DB::raw('CONCAT(ht_customers.first_name, " ", ht_customers.last_name)'), 'LIKE', $kw)
                        ->orWhere('ht_customers.email', 'LIKE', $kw)
                        ->orWhere('pconf_customer_categories.label', 'LIKE', $kw)
                        ->orWhere('pconf_customer_categories.code', 'LIKE', $kw);
                }

                return $query;
            })
            ->editColumn('customer_category', function ($item) {
                if ($item->customer_category_label) {
                    return $item->customer_category_code
                        ? "{$item->customer_category_code} - {$item->customer_category_label}"
                        : $item->customer_category_label;
                }

                return '—';
            })
            ->escapeColumns([]);

        return $this->toJson($data);
    }

    public function query(): Relation|Builder|QueryBuilder
    {
        $query = $this
            ->getModel()
            ->query()
            ->select([
                'ht_customers.id',
                'ht_customers.first_name',
                'ht_customers.last_name',
                'ht_customers.email',
                'ht_customers.customer_category_id',
                'pconf_customer_categories.code as customer_category_code',
                'pconf_customer_categories.label as customer_category_label',
                'ht_customers.created_at',
            ])
            ->leftJoin('pconf_customer_categories', 'ht_customers.customer_category_id', '=', 'pconf_customer_categories.id');

        return $this->applyScopes($query);
    }

    public function columns(): array
    {
        return [
            IdColumn::make(),

            NameColumn::make()
                ->route('customer.edit')
                ->orderable(false)
                ->searchable(false),

            EmailColumn::make()->linkable(),
            'customer_category' => [
                'title' => __('Kategorie'),
                'class' => 'text-start',
                'orderable' => false,
                'searchable' => false,
            ],

            CreatedAtColumn::make(),
        ];
    }

    public function buttons(): array
    {
        return $this->addCreateButton(route('customer.create'), 'customer.create');
    }

    public function bulkActions(): array
    {
        return [
            DeleteBulkAction::make()->permission('customer.destroy'),
        ];
    }

    public function getBulkChanges(): array
    {
        return [
            'first_name' => [
                'title' => trans('plugins/hotel::customer.form.first_name'),
                'type' => 'text',
                'validate' => 'required|max:120',
            ],
            'last_name' => [
                'title' => trans('plugins/hotel::customer.form.last_name'),
                'type' => 'text',
                'validate' => 'required|max:120',
            ],
            'customer_category_id' => [
                'title' => __('Kategorie'),
                'type' => 'select',
                'choices' => CustomerCategory::query()
                    ->pluck('label', 'id')
                    ->toArray(),
                'validate' => 'required|integer|exists:pconf_customer_categories,id',
            ],
            'created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'type' => 'datePicker',
            ],
        ];
    }
}
