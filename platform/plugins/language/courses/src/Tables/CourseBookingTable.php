<?php

namespace Botble\Courses\Tables;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Facades\Html;
use Botble\Courses\Models\CourseBooking;
use Botble\Courses\Models\Course;
use Botble\Hotel\Tables\Formatters\PriceFormatter;
use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\Actions\EditAction;
use Botble\Table\BulkActions\DeleteBulkAction;
use Botble\Table\Columns\Column;
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\StatusColumn;
use Botble\Table\HeaderActions\CreateHeaderAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\JsonResponse;

class CourseBookingTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(CourseBooking::class)
            ->addHeaderAction(CreateHeaderAction::make()->route('course-booking.create'))
            ->addActions([
                EditAction::make()->route('course-booking.edit'),
                DeleteAction::make()->route('course-booking.destroy'),
            ]);
    }

    public function ajax(): JsonResponse
    {
        $data = $this->table
            ->eloquent($this->query())
            ->formatColumn('amount', PriceFormatter::class)

            // ✅ Customer name column
            ->editColumn('customer_id', function (CourseBooking $item) {
                if ($item->customer && $item->customer->id) {
                    return BaseHelper::clean(trim($item->customer->first_name . ' ' . $item->customer->last_name));
                }

                if ($item->address && ($item->address->first_name || $item->address->last_name)) {
                    return BaseHelper::clean(trim($item->address->first_name . ' ' . $item->address->last_name));
                }

                return '&mdash;';
            })

            // ✅ Customer email column
            ->editColumn('customer_email', function (CourseBooking $item) {
                if ($item->customer && $item->customer->email) {
                    return e($item->customer->email);
                }

                if ($item->address && $item->address->email) {
                    return e($item->address->email);
                }

                return '&mdash;';
            })

            // ✅ Customer phone column
            ->editColumn('customer_phone', function (CourseBooking $item) {
                if ($item->customer && $item->customer->phone) {
                    return e($item->customer->phone);
                }

                if ($item->address && $item->address->phone) {
                    return e($item->address->phone);
                }

                return '&mdash;';
            })

            // ✅ Course name column
            ->editColumn('course_id', function (CourseBooking $item) {
                return $item->course && $item->course->id
                    ? Html::link(
                        $item->course->url,
                        BaseHelper::clean($item->course->name),
                        ['target' => '_blank']
                    )
                    : '&mdash;';
            })

            // ✅ Global search filter (LIKE + relations)
            ->filter(function ($query) {
                if ($keyword = $this->request->input('search.value')) {
                    $keyword = '%' . $keyword . '%';

                    return $query
                        // Search by customer name
                        ->whereHas('customer', function ($q) use ($keyword) {
                            $q->where('first_name', 'LIKE', $keyword)
                                ->orWhere('last_name', 'LIKE', $keyword)
                                ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", [$keyword]);
                        })
                        // Search by address name
                        ->orWhereHas('address', function ($q) use ($keyword) {
                            $q->where('first_name', 'LIKE', $keyword)
                                ->orWhere('last_name', 'LIKE', $keyword)
                                ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", [$keyword]);
                        })
                        // Search by email (customer + address)
                        ->orWhereHas('customer', fn($q) => $q->where('email', 'LIKE', $keyword))
                        ->orWhereHas('address', fn($q) => $q->where('email', 'LIKE', $keyword))
                        // Search by phone (customer + address)
                        ->orWhereHas('customer', fn($q) => $q->where('phone', 'LIKE', $keyword))
                        ->orWhereHas('address', fn($q) => $q->where('phone', 'LIKE', $keyword))
                        // Search by course name
                        ->orWhereHas('course', fn($q) => $q->where('name', 'LIKE', $keyword))
                        // Search by payment info
                        ->orWhereHas('payment', function ($q) use ($keyword) {
                            $q->where('payment_channel', 'LIKE', $keyword)
                                ->orWhere('status', 'LIKE', $keyword);
                        })
                        // Direct table fields
                        ->orWhere('amount', 'LIKE', $keyword)
                        ->orWhere('id', 'LIKE', $keyword);
                }

                return $query;
            });

        // ✅ Payment columns setup
        if (!is_plugin_active('payment')) {
            $data = $data->removeColumn('payment_status')->removeColumn('payment_id');
        } else {
            $data = $data
                ->editColumn('payment_status', function (CourseBooking $item) {
                    return $item->payment && $item->payment->status
                        ? BaseHelper::clean($item->payment->status->toHtml())
                        : '&mdash;';
                })
                ->editColumn('payment_id', function (CourseBooking $item) {
                    return $item->payment && $item->payment->payment_channel
                        ? BaseHelper::clean($item->payment->payment_channel->label())
                        : '&mdash;';
                });
        }

        return $this->toJson($data);
    }


    public function query(): Relation|Builder|QueryBuilder
    {
        $query = $this
            ->getModel()
            ->query()
            ->select([
                'id',
                'created_at',
                'status',
                'amount',
                'payment_id',
                'course_id',
                'customer_id',
            ])
            // Wichtig: Kunde + Kurs mitladen, damit E-Mail/Telefon aus der Relation gelesen werden können
            ->with(['customer', 'course']);

        if (is_plugin_active('payment')) {
            $query->with('payment');
        }

        return $this->applyScopes($query);
    }

    public function columns(): array
    {
        // Bestehende Spalten
        $columns = [
            IdColumn::make(),

            Column::make('customer_id')
                ->title(trans('plugins/hotel::booking.customer'))
                ->alignLeft()
                ->orderable(false)
                ->searchable(false),

            // NEU: E-Mail
            Column::make('customer_email')
                ->title(__('E-Mail'))
                ->alignLeft()
                ->width('220px')
                ->orderable(false)    // bewusst ohne Sortierung/Global-Suche (Relation)
                ->searchable(false),

            // NEU: Telefon
            Column::make('customer_phone')
                ->title(__('Telefon'))
                ->alignLeft()
                ->width('160px')
                ->orderable(false)
                ->searchable(false),

            Column::make('course_id')
                ->title(trans('plugins/courses::courses.course.name'))
                ->alignLeft()
                ->orderable(false)
                ->searchable(false),

            Column::formatted('amount')
                ->title(trans('plugins/hotel::booking.amount'))
                ->alignLeft(),

            CreatedAtColumn::make(),
        ];

        if (is_plugin_active('payment')) {
            $columns = array_merge($columns, [
                Column::make('payment_id')
                    ->name('payment_id')
                    ->title(trans('plugins/hotel::booking.payment_method'))
                    ->alignLeft()
                    ->orderable(false)
                    ->searchable(false),

                Column::make('payment_status')
                    ->name('payment_id')
                    ->title(trans('plugins/hotel::booking.payment_status_label'))
                    ->orderable(false)
                    ->searchable(false),
            ]);
        }

        return array_merge($columns, [
            StatusColumn::make(),
        ]);
    }

    public function bulkActions(): array
    {
        return [
            DeleteBulkAction::make()->permission('course-booking.destroy'),
        ];
    }

    public function getBulkChanges(): array
    {
        $options = [
            'customer_name' => [
                'title' => trans('plugins/hotel::booking.customer'),
                'type' => 'text',
                'validate' => 'nullable|string|max:255',
            ],
            'customer_email' => [
                'title' => __('E-Mail'),
                'type' => 'text',
                'validate' => 'nullable|email|max:255',
            ],
            'customer_phone' => [
                'title' => __('Telefon'),
                'type' => 'text',
                'validate' => 'nullable|string|max:255',
            ],
            'course_id' => [
                'title' => trans('plugins/courses::courses.course.name'),
                'type' => 'select',
                'choices' => \Botble\Courses\Models\Course::query()->pluck('name', 'id')->all(),
                'validate' => 'nullable|integer|exists:courses,id',
            ],
            'amount' => [
                'title' => trans('plugins/hotel::booking.amount'),
                'type' => 'text',
                'validate' => 'nullable|numeric|min:0',
            ],
            'status' => [
                'title' => trans('core/base::tables.status'),
                'type' => 'select',
                'choices' => \Botble\Hotel\Enums\BookingStatusEnum::labels(),
                'validate' => 'required|in:' . implode(',', \Botble\Hotel\Enums\BookingStatusEnum::values()),
            ],
            'created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'type' => 'datePicker',
            ],
        ];

        if (is_plugin_active('payment')) {
            $options['payment_status'] = [
                'title' => trans('plugins/hotel::booking.payment_status_label'),
                'type' => 'select',
                'choices' => \Botble\Payment\Enums\PaymentStatusEnum::labels(),
                'validate' => 'required|in:' . implode(',', \Botble\Payment\Enums\PaymentStatusEnum::values()),
            ];
        }

        return $options;
    }

    public function applyFilterCondition(
        Builder|QueryBuilder|Relation $query,
        string $key,
        string $operator,
        ?string $value
    ): Relation|Builder|QueryBuilder {
        if (! $value) {
            return $query;
        }

        switch ($key) {
            // ✅ Customer name filter (checks both first + last name)
            case 'customer_name':
                return $query
                    ->whereHas('customer', function ($q) use ($value) {
                        $q->where('first_name', 'LIKE', '%' . $value . '%')
                            ->orWhere('last_name', 'LIKE', '%' . $value . '%')
                            ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ['%' . $value . '%']);
                    })
                    ->orWhereHas('address', function ($q) use ($value) {
                        $q->where('first_name', 'LIKE', '%' . $value . '%')
                            ->orWhere('last_name', 'LIKE', '%' . $value . '%')
                            ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ['%' . $value . '%']);
                    });

            // ✅ Customer email filter
            case 'customer_email':
                return $query->whereHas('customer', fn ($q) => $q->where('email', 'LIKE', '%' . $value . '%'))
                    ->orWhereHas('address', fn ($q) => $q->where('email', 'LIKE', '%' . $value . '%'));

            // ✅ Customer phone filter
            case 'customer_phone':
                return $query->whereHas('customer', fn ($q) => $q->where('phone', 'LIKE', '%' . $value . '%'))
                    ->orWhereHas('address', fn ($q) => $q->where('phone', 'LIKE', '%' . $value . '%'));

            // ✅ Course filter
            case 'course_id':
                return $query->where('course_id', $value);

            // ✅ Amount filter
            case 'amount':
                return $query->where('amount', 'LIKE', '%' . $value . '%');

            // ✅ Payment status filter
            case 'payment_status':
                return $query->whereHas('payment', fn ($q) => $q->where('status', $value));

            // ✅ Created date filter (ignore time)
            case 'created_at':
                $start = \Carbon\Carbon::parse($value)->startOfDay();
                $end = \Carbon\Carbon::parse($value)->endOfDay();
                return $query->whereBetween('created_at', [$start, $end]);
        }

        // Default (status, etc.)
        return parent::applyFilterCondition($query, $key, $operator, $value);
    }



    public function saveBulkChangeItem(Model|CourseBooking $item, string $inputKey, ?string $inputValue): Model|bool
    {
        if ($inputKey === 'payment_status' && $item instanceof CourseBooking) {
            $item->payment()->update(['status' => $inputValue]);

            return $item;
        }

        return parent::saveBulkChangeItem($item, $inputKey, $inputValue);
    }
}
