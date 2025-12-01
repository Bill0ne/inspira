<?php

namespace Botble\Courses\Providers;

use Botble\Courses\Models\CourseBooking;
use Botble\Courses\Events\CourseBookingCreated;
use Botble\Courses\Services\CourseBookingService;
use Botble\Hotel\Enums\BookingStatusEnum;
use Botble\Hotel\Models\Customer;
use Botble\Payment\Enums\PaymentMethodEnum;
use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\Payment\Models\Payment;
use Botble\Payment\Supports\PaymentHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class HookServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        add_filter(BASE_FILTER_ENUM_ARRAY, function ($values, $class) {
            if ($class === PaymentMethodEnum::class) {
                $values['CUSTOMER_CARD'] = 'customer_card';
            }

            return $values;
        }, 120, 2);

//        if (defined('PAYMENT_FILTER_REDIRECT_URL')) {
//            add_filter(PAYMENT_FILTER_REDIRECT_URL, function ($checkoutToken) {
//                return route('public.course.booking.information', $checkoutToken ?: session('course_booking_transaction_id'));
//            }, 999);
//        }
//
//        if (defined('PAYMENT_FILTER_CANCEL_URL')) {
//            add_filter(PAYMENT_FILTER_CANCEL_URL, function ($checkoutToken) {
//                return route('public.course.booking.form', [
//                    'token' => $checkoutToken ?: session('checkout_token'),
//                    'error' => true,
//                    'error_type' => 'payment',
//                ]);
//            }, 999);
//        }

        if (defined('PAYMENT_ACTION_PAYMENT_PROCESSED')) {
            add_action(PAYMENT_ACTION_PAYMENT_PROCESSED, function ($data) {
                $orderIds = (array) Arr::get($data, 'order_id', []);
                $orderId = Arr::first($orderIds);
                $orderType = Arr::get($data, 'order_type') ?: session('order_type');

                if ($orderType !== CourseBooking::class || ! $orderId) {
                    return;
                }

                $payment = null;

                if (is_plugin_active('payment')) {
                    $payment = Payment::query()->where('charge_id', Arr::get($data, 'charge_id'))->first();

                    if (! $payment) {
                        $payment = PaymentHelper::storeLocalPayment($data);
                    }
                }

                /** @var CourseBookingService $bookingService */
                $bookingService = app(CourseBookingService::class);

                $booking = $bookingService->processBooking($orderId, $payment?->charge_id);

                if (! $booking) {
                    return;
                }

                if ($payment) {
                    $paymentMethod = $bookingService->normalizePaymentChannel($payment->payment_channel);

                    $booking->forceFill([
                        'payment_id' => $payment->getKey(),
                        'payment_method' => $paymentMethod,
                    ]);

                    if ($payment->status === PaymentStatusEnum::COMPLETED) {
                        $booking->status = BookingStatusEnum::PROCESSING;
                    }
                }

                if ($booking->customer_card_id && ! $booking->payment_method) {
                    $booking->payment_method = $bookingService->normalizePaymentChannel(
                        PaymentMethodEnum::CUSTOMER_CARD()
                    );
                }

                if ($booking->isDirty()) {
                    $booking->save();
                }

                $booking->refresh();

                $customerCardMethod = $bookingService->normalizePaymentChannel(PaymentMethodEnum::CUSTOMER_CARD());

                if ($booking->customer_card_id) {
                    if (
                        $booking->status !== BookingStatusEnum::PROCESSING
                        && (! $payment || $payment->status === PaymentStatusEnum::COMPLETED)
                    ) {
                        $booking->status = BookingStatusEnum::PROCESSING;
                        $booking->save();
                    }

                    $bookingService->finalizeCustomerCardUsage($booking->refresh());
                }
            });
        }

        Event::listen(CourseBookingCreated::class, function (CourseBookingCreated $event) {
            $booking = $event->courseBooking;

            if (! $booking->customer_card_id) {
                return;
            }

            if ($booking->customer_card_consumed_at) {
                return;
            }

            if ($booking->amount > 0 && $booking->payment_id) {
                return;
            }

            app(CourseBookingService::class)->finalizeCustomerCardUsage($booking);
        });

        if (defined('PAYMENT_COURSE_FILTER_PAYMENT_DATA')) {
            add_filter(PAYMENT_COURSE_FILTER_PAYMENT_DATA, function (array $data, Request $request) {
                $orderIds = (array) $request->input('order_id', []);

                $booking = CourseBooking::query()->find(Arr::first($orderIds));

                if (! $booking) {
                    return array_merge($data, [
                        'amount' => 0,
                        'currency' => strtoupper(get_application_currency()->title),
                        'order_id' => $orderIds,
                        'products' => [],
                    ]);
                }

                return [
                    'amount' => (float) $booking->amount,
                    'shipping_amount' => 0,
                    'order_type' => \Botble\Courses\Models\CourseBooking::class,
                    'shipping_method' => null,
                    'tax_amount' => $booking->tax_amount ?? 0,
                    'discount_amount' => $booking->discount_amount ?? 0,
                    'currency' => strtoupper(get_application_currency()->title),
                    'order_id' => $orderIds,
                    'description' => trans('plugins/payment::payment.payment_description', [
                        'order_id' => Arr::first($orderIds),
                        'site_url' => request()->getHost(),
                    ]),
                    'customer_id' => auth('customer')->check() ? auth('customer')->id() : null,
                    'customer_type' => Customer::class,
                    'return_url' => $request->input('return_url'),
                    'callback_url' => $request->input('callback_url'),
                    'products' => [
                        [
                            'id' => $booking->getKey(),
                            'name' => $booking->course->name ?? 'Course',
                            'image' => $booking->course->image ?? null,
                            'price' => $booking->amount,
                            'price_per_order' => $booking->amount,
                            'qty' => 1,
                        ],
                    ],
                    'orders' => [$booking],
                    'address' => [],
                    'checkout_token' => session('checkout_token'),
                ];
            }, 140, 2);
        }

        if (defined('PAYMENT_FILTER_PAYMENT_INFO_DETAIL')) {
            add_filter(PAYMENT_FILTER_PAYMENT_INFO_DETAIL, function ($html, $payment) {
                if (! $payment->order_id) {
                    return $html;
                }

                $booking = CourseBooking::query()->find($payment->order_id);

                if (! $booking) {
                    return $html;
                }

                return view('plugins/hotel::partials.payment-info', compact('booking'))->render() . $html;
            }, 183, 2);
        }

        if (defined('ACTION_AFTER_UPDATE_PAYMENT')) {
            add_action(ACTION_AFTER_UPDATE_PAYMENT, function ($request, $payment): void {
                if (
                    in_array($payment->payment_channel, [PaymentMethodEnum::COD, PaymentMethodEnum::BANK_TRANSFER])
                    && $request->input('status') == PaymentStatusEnum::COMPLETED
                ) {
                    do_action(PAYMENT_ACTION_PAYMENT_PROCESSED, [
                        'amount' => (float) $payment->amount,
                        'currency' => $payment->currency,
                        'charge_id' => $payment->charge_id,
                        'payment_channel' => $payment->payment_channel,
                        'status' => PaymentStatusEnum::COMPLETED,
                        'order_id' => [$payment->order_id],
                        'order_type' => CourseBooking::class,
                        'customer_id' => $payment->customer_id,
                        'customer_type' => $payment->customer_type,
                    ]);
                }

                $orderIds = (array) ($request->input('order_id', []) ?: []);
                $primaryOrderId = Arr::first($orderIds) ?: $payment->order_id;

                if (! $primaryOrderId) {
                    return;
                }

                if ($payment->order_type && $payment->order_type !== CourseBooking::class) {
                    return;
                }

                $booking = CourseBooking::query()->find($primaryOrderId);

                if (! $booking) {
                    return;
                }

                $bookingService = app(CourseBookingService::class);

                $updates = [];

                if (! $booking->payment_id) {
                    $updates['payment_id'] = $payment->getKey();
                }

                if (! $booking->payment_method && $payment->payment_channel) {
                    $updates['payment_method'] = $bookingService->normalizePaymentChannel($payment->payment_channel);
                }

                if ($updates) {
                    $booking->forceFill($updates)->save();
                }

                if (
                    $payment->status === PaymentStatusEnum::COMPLETED
                    && $booking->customer_card_id
                    && ! $booking->customer_card_consumed_at
                ) {
                    $bookingService->finalizeCustomerCardUsage($booking->refresh());
                }
            }, 183, 2);
        }


        add_filter(BASE_FILTER_GET_LIST_DATA, function ($data, $model) {
            if ($model instanceof Payment) {
                return $data->addColumn('customer_id', function ($item) {
                    $booking = CourseBooking::query()->find($item->order_id);
                    return $booking ? ($booking->customer_name ?? '&mdash;') : '&mdash;';
                });
            }

            return $data;
        }, 123, 2);
    }
}
