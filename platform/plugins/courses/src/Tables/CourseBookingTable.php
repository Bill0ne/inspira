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
use Illuminate\Support\Arr;

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

            ->editColumn('customer_id', function (CourseBooking $item) {
                if ($item->customer && $item->customer->id) {
                    return BaseHelper::clean(trim($item->customer->first_name . ' ' . $item->customer->last_name));
                }

                if ($item->address && ($item->address->first_name || $item->address->last_name)) {
                    return BaseHelper::clean(trim($item->address->first_name . ' ' . $item->address->last_name));
                }

                return '&mdash;';
            })

            ->editColumn('customer_email', function (CourseBooking $item) {
                if ($item->customer && $item->customer->email) {
                    return e($item->customer->email);
                }

                if ($item->address && $item->address->email) {
                    return e($item->address->email);
                }

                return '&mdash;';
            })

            ->editColumn('customer_phone', function (CourseBooking $item) {
                if ($item->customer && $item->customer->phone) {
                    return e($item->customer->phone);
                }

                if ($item->address && $item->address->phone) {
                    return e($item->address->phone);
                }

                return '&mdash;';
            })

            ->editColumn('course_id', function (CourseBooking $item) {
                return $item->course && $item->course->id
                    ? Html::link(
                        $item->course->url,
                        BaseHelper::clean($item->course->name),
                        ['target' => '_blank']
                    )
                    : '&mdash;';
            })

            ->filter(function ($query) {
                if ($keyword = $this->request->input('search.value')) {
                    $keyword = '%' . $keyword . '%';

                    $query->where('status', '!=', \Botble\Hotel\Enums\BookingStatusEnum::AWAITING_PAYMENT)
                        ->where(function ($q) use ($keyword) {
                            $q->whereHas('customer', function ($q2) use ($keyword) {
                                $q2->where('first_name', 'LIKE', $keyword)
                                    ->orWhere('last_name', 'LIKE', $keyword)
                                    ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", [$keyword]);
                            })
                                ->orWhereHas('address', function ($q2) use ($keyword) {
                                    $q2->where('first_name', 'LIKE', $keyword)
                                        ->orWhere('last_name', 'LIKE', $keyword)
                                        ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", [$keyword]);
                                })
                                ->orWhereHas('customer', fn($q2) => $q2->where('email', 'LIKE', $keyword))
                                ->orWhereHas('address', fn($q2) => $q2->where('email', 'LIKE', $keyword))
                                ->orWhereHas('customer', fn($q2) => $q2->where('phone', 'LIKE', $keyword))
                                ->orWhereHas('address', fn($q2) => $q2->where('phone', 'LIKE', $keyword))
                                ->orWhereHas('course', fn($q2) => $q2->where('name', 'LIKE', $keyword))
                                ->orWhereHas('payment', function ($q2) use ($keyword) {
                                    $q2->where('payment_channel', 'LIKE', $keyword)
                                        ->orWhere('status', 'LIKE', $keyword);
                                })
                                ->orWhere('amount', 'LIKE', $keyword)
                                ->orWhere('id', 'LIKE', $keyword);
                        });
                } else {
                    $query->where('status', '!=', \Botble\Hotel\Enums\BookingStatusEnum::AWAITING_PAYMENT);
                }

                return $query;
            });

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
            ->with(['customer', 'course'])
            ->where('status', '!=', \Botble\Hotel\Enums\BookingStatusEnum::AWAITING_PAYMENT);;

        if (is_plugin_active('payment')) {
            $query->with('payment');
        }

        return $this->applyScopes($query);
    }

    public function columns(): array
    {
        $columns = [
            IdColumn::make(),

            Column::make('customer_id')
                ->title(trans('plugins/hotel::booking.customer'))
                ->alignLeft()
                ->orderable(false)
                ->searchable(false),

            Column::make('customer_email')
                ->title(__('E-Mail'))
                ->alignLeft()
                ->width('220px')
                ->orderable(false)
                ->searchable(false),

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
        $methods = \Botble\Hotel\Enums\BookingStatusEnum::labels();
        Arr::forget($methods, \Botble\Hotel\Enums\BookingStatusEnum::AWAITING_PAYMENT);
        $options = [
            'customer_name' => [
                'title' => __('Customer Name'),
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
                'choices' => $methods,
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

            case 'customer_email':
                return $query->whereHas('customer', fn ($q) => $q->where('email', 'LIKE', '%' . $value . '%'))
                    ->orWhereHas('address', fn ($q) => $q->where('email', 'LIKE', '%' . $value . '%'));

            case 'customer_phone':
                return $query->whereHas('customer', fn ($q) => $q->where('phone', 'LIKE', '%' . $value . '%'))
                    ->orWhereHas('address', fn ($q) => $q->where('phone', 'LIKE', '%' . $value . '%'));

            case 'course_id':
                return $query->where('course_id', $value);

            case 'amount':
                return $query->where('amount', 'LIKE', '%' . $value . '%');

            case 'payment_status':
                return $query->whereHas('payment', fn ($q) => $q->where('status', $value));

            case 'created_at':
                $start = \Carbon\Carbon::parse($value)->startOfDay();
                $end = \Carbon\Carbon::parse($value)->endOfDay();
                return $query->whereBetween('created_at', [$start, $end]);
        }

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
