<?php

namespace Botble\Courses\Http\Controllers;

use Botble\Base\Facades\Assets;
use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Courses\Models\CourseBooking;
use Botble\Courses\Tables\CourseBookingTable;
use Botble\Courses\Events\CourseBookingCreated;
use Botble\Courses\Http\Requests\CreateCourseBookingRequest;
use Botble\Courses\Http\Requests\UpdateBookingCourseRequest;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Courses\Forms\CourseBookingCreateForm;
use Botble\Courses\Forms\CourseBookingForm;
use Botble\Courses\Events\CourseBookingUpdated;
use Botble\Courses\Events\CourseBookingChangedStatus;
use Botble\Hotel\Models\Customer;
use Botble\Courses\Models\Course;
use Botble\Payment\Enums\PaymentMethodEnum;
use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\Payment\Models\Payment;
use Botble\Payment\Services\Gateways\BankTransferPaymentService;
use Botble\Payment\Services\Gateways\CodPaymentService;
use Botble\Payment\Supports\PaymentHelper;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


class CourseBookingController extends BaseController
{
    public function __construct()
    {
        $this
            ->breadcrumb()
            ->add(trans('plugins/hotel::booking.name'), route('booking.index'));
    }

    public function index(CourseBookingTable $table)
    {
        $this->pageTitle(trans('plugins/hotel::booking.name'));

        return $table->renderTable();
    }

    public function destroy(CourseBooking $courseBooking)
    {
        return DeleteResourceAction::make($courseBooking);
    }

    public function create()
    {
        $this->pageTitle(trans('plugins/hotel::booking.create'));

        return CourseBookingCreateForm::create()->renderForm();
    }

    public function store(CreateCourseBookingRequest $request, BaseHttpResponse $response)
    {
        $customerId = $request->input('customer_id');

        if (! $customerId || $customerId == '0') {
            $customer = Customer::query()->create([
                'first_name' => $request->input('first_name'),
                'last_name' => $request->input('last_name'),
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
            ]);
            $customerId = $customer->id;
        }

        /**
         * @var Course $course
         */
        $course = Course::query()->findOrFail($request->input('course_id'));

        $booking = new CourseBooking();
        $booking->fill([
            'status' => $request->input('status'),
            'customer_id' => $customerId,
            'requests' => $request->input('requests'),
            'transaction_id' => Str::upper(Str::random(32)),
            'booking_number' => CourseBooking::generateUniqueBookingNumber(),
            'course_id' => $course->id,
        ]);

        $amount = $course->price ?? 0;

        $taxAmount = 0;
        if ($course->tax) {
            $taxAmount = $course->tax->percentage * $amount / 100;
        }

        $booking->amount = $amount + $taxAmount;
        $booking->sub_total = $amount;
        $booking->tax_amount = $taxAmount;
        $booking->save();

        if (is_plugin_active('payment')) {
            $paymentData = [
                'amount' => $booking->amount,
                'currency' => get_application_currency()->title,
                'charge_id' => $request->input('transaction_id') ?: Str::upper(Str::random(20)),
                'order_id' => $booking->id,
                'customer_id' => $customerId,
                'customer_type' => Customer::class,
                'payment_channel' => $request->input('payment_method'),
                'status' => $request->input('payment_status', PaymentStatusEnum::PENDING),
            ];

            $payment = null;

            switch ($request->input('payment_method')) {
                case PaymentMethodEnum::COD:
                    $codPaymentService = app(CodPaymentService::class);
                    $codPaymentService->execute($paymentData);
                    break;

                case PaymentMethodEnum::BANK_TRANSFER:
                    $bankTransferPaymentService = app(BankTransferPaymentService::class);
                    $bankTransferPaymentService->execute($paymentData);
                    break;

                default:
                    $payment = PaymentHelper::storeLocalPayment($paymentData);
                    break;
            }

            if (! $payment) {
                $payment = Payment::query()
                    ->where('charge_id', $paymentData['charge_id'])
                    ->where('order_id', $booking->id)
                    ->first();
            }

            if ($payment) {
                $payment->status = $request->input('payment_status', PaymentStatusEnum::PENDING);
                $booking->payment_id = $payment->id;
                $booking->save();
            }
        }

        CourseBookingCreated::dispatch($booking);

        return $response
            ->setPreviousUrl(route('course-booking.index'))
            ->setNextUrl(route('course-booking.edit', $booking->id))
            ->withCreatedSuccessMessage();
    }



    public function edit(CourseBooking $courseBooking)
    {
        $this->pageTitle(trans('core/base::forms.edit_item', ['name' => $courseBooking->course->name]));

        return CourseBookingForm::createFromModel($courseBooking)->renderForm();
    }

    public function update(CourseBooking $courseBooking, UpdateBookingCourseRequest $request)
    {
        $status = $courseBooking->status;

        CourseBookingForm::createFromModel($courseBooking)
            ->setRequest($request)
            ->save();

        CourseBookingUpdated::dispatch($courseBooking);

        if ($courseBooking->status != $status) {
            CourseBookingChangedStatus::dispatch($status, $courseBooking);
        }

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('course-booking.index'))
            ->withUpdatedSuccessMessage();
    }

}
