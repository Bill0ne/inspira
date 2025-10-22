<?php

namespace Botble\Courses\Forms;

use Botble\Base\Facades\Assets;
use Botble\Base\Forms\FormAbstract;
use Botble\Courses\Models\Course;
use Botble\Courses\Models\CourseBooking;
use Botble\Courses\Http\Requests\CreateCourseBookingRequest;
use Botble\Hotel\Enums\BookingStatusEnum;
use Botble\Payment\Enums\PaymentMethodEnum;
use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Forms\Fields\{TextField, EmailField, SelectField, TextareaField};
use Botble\Base\Forms\FieldOptions\{TextFieldOption, EmailFieldOption, SelectFieldOption, TextareaFieldOption, StatusFieldOption};

class CourseBookingCreateForm extends FormAbstract
{
    public function setup(): void
    {
        Assets::addScripts(['booking-create']);
        Assets::addScriptsDirectly('vendor/core/plugins/hotel/js/booking-room-search.js');
        Assets::addScriptsDirectly('vendor/core/plugins/hotel/js/customer-autocomplete.js');

        $this
            ->model(CourseBooking::class)
            ->setValidatorClass(CreateCourseBookingRequest::class)
            ->withCustomFields()
            ->columns()
            ->add('customer_search_container', 'html', [
                'html' => '
                <div class="form-group mb-3">
                    <label for="customer_search">' . trans('plugins/hotel::booking.customer') . '</label>
                    <input type="text" id="customer_search" class="form-control" placeholder="' . trans('plugins/hotel::booking.search_customer') . '">
                    <div id="customer_search_results" class="dropdown-menu w-100" style="display: none;"></div>
                    <button class="btn btn-sm btn-info mt-2" type="button" id="btn_create_new_customer">
                        ' . trans('plugins/hotel::booking.create_new_customer') . '
                    </button>
                    <div id="selected_customer_info" class="mt-2" style="display: none;"></div>
                </div>
                ' . view('plugins/hotel::customer-create-modal')->render(),
                'colspan' => 2,
            ])
            ->add(
                'customer_id',
                'hidden',
                [
                    'value' => 0,
                    'attr' => [
                        'id' => 'customer_id',
                    ],
                ]
            )
            ->add(
                'course_id',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(__('Select Course'))
                    ->required()
                    ->choices(Course::query()->wherePublished()->pluck('name', 'id')->all())
                    ->helperText(__('Choose the course to book'))
                    ->colspan(2)
            )
            ->add(
                'payment_method',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(__('Payment Method'))
                    ->choices(PaymentMethodEnum::labels())
                    ->helperText(__('Select payment method'))
                    ->colspan(1)
            )
            ->add(
                'payment_status',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(__('Payment Status'))
                    ->choices(PaymentStatusEnum::labels())
                    ->defaultValue(PaymentStatusEnum::PENDING)
                    ->colspan(1)
            )
            ->add(
                'transaction_id',
                TextField::class,
                TextFieldOption::make()
                    ->label(__('Transaction ID'))
                    ->placeholder(__('Transaction ID (optional)'))
                    ->colspan(2)
            )
            ->add(
                'status',
                SelectField::class,
                StatusFieldOption::make()
                    ->choices(BookingStatusEnum::labels())
                    ->defaultValue(BookingStatusEnum::PENDING)
                    ->colspan(2)
            )
            ->setBreakFieldPoint('status');
    }
}
