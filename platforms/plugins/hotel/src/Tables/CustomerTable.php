<?php

namespace Botble\Hotel\Tables;

use Botble\Hotel\Models\Customer;
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

            // Badge-Ausgabe mit Farben:
            // STANDARD = grau, andere = #578e88 (weiß)
            ->editColumn('customer_category', function (Customer $item) {
                $code = trim((string)($item->customer_category ?? '')) ?: 'STANDARD';
                $codeUpper = strtoupper($code);

                if ($codeUpper === 'STANDARD') {
                    $style = 'display:inline-block;padding:2px 8px;border-radius:12px;font-size:12px;line-height:1;'
                        . 'background:#e9ecef;color:#495057;border:1px solid #ced4da;';
                } else {
                    $style = 'display:inline-block;padding:2px 8px;border-radius:12px;font-size:12px;line-height:1;'
                        . 'background:#578e88;color:#ffffff;';
                }

                return '<span style="' . $style . '">' . e($codeUpper) . '</span>';
            })

            // Suche auch über E-Mail & Kategorie
            ->filter(function ($query) {
                $keyword = $this->request->input('search.value');

                if ($keyword) {
                    $kw = '%' . $keyword . '%';

                    return $query
                        ->where('first_name', 'LIKE', $kw)
                        ->orWhere('last_name', 'LIKE', $kw)
                        ->orWhere(DB::raw('CONCAT(first_name, " ", last_name)'), 'LIKE', $kw)
                        ->orWhere(DB::raw('CONCAT(last_name, " ", first_name)'), 'LIKE', $kw)
                        ->orWhere('email', 'LIKE', $kw)
                        ->orWhere('customer_category', 'LIKE', $kw);
                }

                return $query;
            })

            // HTML für Badge erlauben
            ->escapeColumns([]);

        return $this->toJson($data);
    }

    public function query(): Relation|Builder|QueryBuilder
    {
        // Fallback auf STANDARD schon in SQL (alte Datensätze / NULL)
        $query = $this
            ->getModel()
            ->query()
            ->select([
                'id',
                'first_name',
                'last_name',
                'email',
                DB::raw("COALESCE(customer_category, 'STANDARD') as customer_category"),
                'created_at',
            ]);

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

            // Neue Spalte: Kategorie (Badge via ajax()->editColumn)
            'customer_category' => [
                'title' => 'Kategorie',
                'class' => 'text-center',
                'width' => '140px',
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
        // Dropdown-Choices für Bulk-Änderung laden (inkl. STANDARD)
        $categoryChoices = ['STANDARD' => 'STANDARD'];
        if (DB::getSchemaBuilder()->hasTable('pc_customer_categories')) {
            $categoryChoices = DB::table('pc_customer_categories')
                ->where('status', 'active')
                ->orderBy('code')
                ->pluck('label', 'code') // ['STANDARD' => 'Standard', 'VIP' => 'VIP', ...]
                ->toArray();

            // Anzeige konsistent als CODE
            foreach ($categoryChoices as $code => $label) {
                $categoryChoices[$code] = strtoupper($code);
            }
            $categoryChoices = ['STANDARD' => 'STANDARD'] + $categoryChoices;
        }

        return [
            'first_name' => [
                'title' => trans('plugins/hotel::customer.form.first_name'),
                'type' => 'text',
                'validate' => 'required|max:120',
            ],
            'last_name' => [
                'title' => trans('plugins/hotel::customer.form.lst_name'),
                'type' => 'text',
                'validate' => 'required|max:120',
            ],
            'customer_category' => [
                'title' => 'Kategorie',
                'type' => 'select',
                'choices' => $categoryChoices,
                'validate' => 'required|string|max:191',
            ],
            'created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'type' => 'datePicker',
            ],
        ];
    }
}
