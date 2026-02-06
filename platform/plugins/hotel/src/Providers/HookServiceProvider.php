<?php

namespace Botble\Hotel\Providers;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Hotel\Enums\BookingStatusEnum;
use Botble\Hotel\Models\Booking;
use Botble\Hotel\Models\Customer;
use Botble\Hotel\Models\CustomerCardOrder;
use Botble\Hotel\Services\BookingService;
use Botble\Hotel\Services\CustomerCardPurchaseService;
use Botble\Media\Facades\RvMedia;
use Botble\Payment\Enums\PaymentMethodEnum;
use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\Payment\Models\Payment;
use Botble\Payment\Supports\PaymentHelper;
use Botble\Table\Columns\Column;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class HookServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        add_filter(BASE_FILTER_TOP_HEADER_LAYOUT, [$this, 'registerTopHeaderNotification'], 140);
        add_filter(BASE_FILTER_APPEND_MENU_NAME, [$this, 'countPendingBookings'], 140, 2);
        add_filter(BASE_FILTER_MENU_ITEMS_COUNT, [$this, 'getMenuItemCount'], 140);

        if (defined('PAYMENT_FILTER_REDIRECT_URL')) {
            add_filter(PAYMENT_FILTER_REDIRECT_URL, function ($checkoutToken) {
                // Fallback: Falls die Session geleert wurde, aber der Payment-Callback
                // nur den Checkout-/Charge-Token mitbringt, können wir den Order-Typ
                // aus der gespeicherten Zahlung rekonstruieren.
                $payment = Payment::query()
                    ->where('charge_id', $checkoutToken)
                    ->orWhere('payment_channel', $checkoutToken)
                    ->latest('created_at')
                    ->first();

                if ($payment && $payment->order_type && ! session()->has('order_type')) {
                    session(['order_type' => $payment->order_type]);
                }

                if (session()->has('course_booking_transaction_id')) {
                    return route(
                        'public.course.booking.information',
                        $checkoutToken ?: session('course_booking_transaction_id')
                    );
                }

                if (session()->has('booking_transaction_id')) {
                    return route(
                        'public.booking.information',
                        $checkoutToken ?: session('booking_transaction_id')
                    );
                }

                if (session('order_type') === CustomerCardOrder::class) {
                    return route('customer.cards.success');
                }

                return url('/');
            }, 999);
        }

        if (defined('PAYMENT_FILTER_CANCEL_URL')) {
            add_filter(PAYMENT_FILTER_CANCEL_URL, function ($checkoutToken) {
                if (session()->has('checkout_token')) {
                    if (session()->has('course_booking_transaction_id')) {
                        return route('public.course.booking.form', [
                            'token' => $checkoutToken ?: session('checkout_token'),
                            'error' => true,
                            'error_type' => 'payment',
                        ]);
                    }

                    if (session()->has('booking_transaction_id')) {
                        return route('public.booking.form', [
                            'token' => $checkoutToken ?: session('checkout_token'),
                            'error' => true,
                            'error_type' => 'payment',
                        ]);
                    }
                }

                return url('/');
            }, 999);
        }

        if (defined('PAYMENT_ACTION_PAYMENT_PROCESSED')) {
            add_action(PAYMENT_ACTION_PAYMENT_PROCESSED, function ($data) {
                $orderIds = (array) $data['order_id'];
                $orderId = Arr::first($orderIds);

                // Payment in der lokalen DB speichern (Botble-Standard)
                PaymentHelper::storeLocalPayment($data);

                // Typ der Bestellung (Hotel-Booking, CourseBooking, CustomerCardOrder, …)
                $orderType = $data['order_type'] ?? session('order_type');

                // Session-Wert nach Verwendung aufräumen, um spätere Zahlungen nicht zu beeinflussen
                if (! array_key_exists('order_type', $data) && session()->has('order_type')) {
                    session()->forget('order_type');
                }

                switch ($orderType) {
                    case \Botble\Hotel\Models\Booking::class:
                        return app(BookingService::class)
                            ->processBooking($orderId, $data['charge_id'] ?? null);

                    case CustomerCardOrder::class:
                        if (! $orderId && empty($data['charge_id'])) {
                            return null;
                        }

                        return app(CustomerCardPurchaseService::class)
                            ->completeOrder($orderId, $data['charge_id'] ?? null);

                    default:
                        return null;
                }
            });
        }

        if (defined('PAYMENT_FILTER_PAYMENT_DATA')) {
            add_filter(PAYMENT_FILTER_PAYMENT_DATA, function (array $data, Request $request) {
                $orderIds = (array) $request->input('order_id', []);
                $orderType = Arr::get($data, 'order_type') ?: $request->input('order_type');

                // Customer Card Bestellungen
                if ($orderType === 'customer_card') {
                    $orderType = CustomerCardOrder::class;
                }

                if ($orderType === CustomerCardOrder::class) {
                    $order = null;

                    if (! empty($orderIds)) {
                        $order = CustomerCardOrder::query()
                            ->with('template')
                            ->find(Arr::first($orderIds));
                    }

                    if ($order) {
                        return array_merge($data, [
                            'amount'          => (float) $order->amount,
                            'shipping_amount' => 0,
                            'shipping_method' => null,
                            'tax_amount'      => 0,
                            'discount_amount' => 0,
                            'currency'        => strtoupper(get_application_currency()->title),
                            'order_id'        => $orderIds,
                            'description'     => trans(
                                'plugins/payment::payment.payment_description',
                                [
                                    'order_id' => Arr::first($orderIds),
                                    'site_url' => request()->getHost(),
                                ]
                            ),
                            'customer_id'   => $order->customer_id,
                            'customer_type' => Customer::class,
                            'return_url'    => $request->input('return_url', route('customer.cards')),
                            'callback_url'  => $request->input('callback_url', route('customer.cards')),
                            'products'      => [
                                [
                                    'id'              => $order->card_template_id,
                                    'name'            => $order->template->name ?? 'Customer card',
                                    'image'           => null,
                                    'price'           => $order->amount,
                                    'price_per_order' => $order->amount,
                                    'qty'             => 1,
                                ],
                            ],
                            'orders'        => [$order],
                            'address'       => [],
                            'checkout_token'=> session('checkout_token'),
                            'order_type'    => CustomerCardOrder::class,
                        ]);
                    }

                    $amount = (float) $request->input('amount', Arr::get($data, 'amount', 0));

                    return array_merge($data, [
                        'amount'          => $amount,
                        'currency'        => strtoupper(get_application_currency()->title),
                        'order_id'        => $orderIds,
                        'order_type'      => CustomerCardOrder::class,
                        'return_url'      => $request->input('return_url', route('customer.cards')),
                        'callback_url'    => $request->input('callback_url', route('customer.cards')),
                        'customer_id'     => auth('customer')->check() ? auth('customer')->id() : null,
                        'customer_type'   => Customer::class,
                        'products'        => $amount > 0 ? [[
                            'id'              => null,
                            'name'            => 'Customer card',
                            'image'           => null,
                            'price'           => $amount,
                            'price_per_order' => $amount,
                            'qty'             => 1,
                        ]] : [],
                        'orders'          => [],
                        'address'         => [],
                        'checkout_token'  => session('checkout_token'),
                    ]);
                }

                // Hotel-Booking Payment-Daten
                $booking = Booking::query()->find(Arr::first($orderIds));

                if (! $booking) {
                    return $data;
                }

                $rooms = [
                    [
                        'id'              => $booking->getKey(),
                        'name'            => $booking->room->room->name,
                        'image'           => RvMedia::getImageUrl($booking->room->room->image),
                        'price'           => $booking->amount + $booking->tax_amount - $booking->coupon_amount,
                        'price_per_order' => $booking->amount,
                        'qty'             => 1,
                    ],
                ];

                $address = [
                    'name'    => $booking->address->first_name . ' ' . $booking->address->last_name,
                    'email'   => $booking->address->email,
                    'phone'   => $booking->address->phone,
                    'country' => $booking->address->country,
                    'state'   => $booking->address->state,
                    'city'    => $booking->address->city,
                    'address' => $booking->address->address,
                    'zip'     => $booking->address->zip,
                ];

                return array_merge($data, [
                    'amount'          => (float) $booking->amount,
                    'shipping_amount' => 0,
                    'shipping_method' => null,
                    'tax_amount'      => $booking->tax_amount,
                    'discount_amount' => $booking->coupon_amount,
                    'currency'        => strtoupper(get_application_currency()->title),
                    'order_id'        => $orderIds,
                    'description'     => trans(
                        'plugins/payment::payment.payment_description',
                        [
                            'order_id' => Arr::first($orderIds),
                            'site_url' => request()->getHost(),
                        ]
                    ),
                    'customer_id'     => auth('customer')->check() ? auth('customer')->id() : null,
                    'customer_type'   => Customer::class,
                    'return_url'      => $request->input('return_url'),
                    'callback_url'    => $request->input('callback_url'),
                    'products'        => $rooms,
                    'orders'          => [$booking],
                    'address'         => $address,
                    'checkout_token'  => session('checkout_token'),
                    'order_type'      => Booking::class,
                ]);
            }, 140, 2);
        }

        if (defined('PAYMENT_FILTER_PAYMENT_INFO_DETAIL')) {
            add_filter(PAYMENT_FILTER_PAYMENT_INFO_DETAIL, function ($html, $payment) {
                if (! $payment->order_id) {
                    return $html;
                }

                $booking = Booking::query()->find($payment->order_id);

                if (! $booking || ! $booking->address) {
                    return $html;
                }

                return view('plugins/hotel::partials.payment-info', compact('booking'))->render() . $html;
            }, 123, 2);
        }

        if (defined('ACTION_AFTER_UPDATE_PAYMENT')) {
            add_action(ACTION_AFTER_UPDATE_PAYMENT, function ($request, $payment): void {
                if (
                    in_array($payment->payment_channel, [PaymentMethodEnum::COD, PaymentMethodEnum::BANK_TRANSFER], true)
                    && $request->input('status') == PaymentStatusEnum::COMPLETED
                ) {
                    Booking::query()
                        ->where('payment_id', $payment->id)
                        ->update(['status' => BookingStatusEnum::PROCESSING]);

                    CustomerCardOrder::query()
                        ->where('payment_id', $payment->id)
                        ->get()
                        ->each(function (CustomerCardOrder $order) {
                            app(CustomerCardPurchaseService::class)->finalizeOrder($order);
                        });
                }
            }, 123, 2);
        }

        add_filter(BASE_FILTER_GET_LIST_DATA, function ($data, $model) {
            if (get_class($model) == Payment::class) {
                return $data
                    ->addColumn('customer_id', function ($item) {
                        if (! $item->order_id) {
                            return '&mdash;';
                        }

                        $booking = Booking::query()->find($item->order_id);

                        if (! $booking) {
                            return '&mdash;';
                        }

                        return $booking->address->first_name . ' ' . $booking->address->last_name;
                    })
                    ->filter(function ($query) {
                        $keyword = request()->input('search.value');
                        if ($keyword) {
                            return $query
                                ->join('ht_bookings', 'ht_bookings.id', '=', 'payments.order_id')
                                ->join('ht_booking_addresses', 'ht_booking_addresses.booking_id', '=', 'ht_bookings.id')
                                ->where(function ($subQuery) use ($keyword) {
                                    return $subQuery
                                        ->where('ht_booking_addresses.first_name', 'LIKE', '%' . $keyword . '%')
                                        ->orWhere('ht_booking_addresses.last_name', 'LIKE', '%' . $keyword . '%')
                                        ->orWhere(
                                            DB::raw('CONCAT(ht_booking_addresses.first_name, " ", ht_booking_addresses.last_name)'),
                                            'LIKE',
                                            '%' . $keyword . '%'
                                        )
                                        ->orWhere(
                                            DB::raw('CONCAT(ht_booking_addresses.last_name, " ", ht_booking_addresses.first_name)'),
                                            'LIKE',
                                            '%' . $keyword . '%'
                                        );
                                })
                                ->select('payments.*');
                        }

                        return $query;
                    });
            }

            return $data;
        }, 123, 2);

        add_filter(BASE_FILTER_TABLE_HEADINGS, function ($headings, $model) {
            if ($model instanceof Payment) {
                return [
                    ...$headings,
                    Column::make('customer_id')
                        ->label(trans('plugins/hotel::booking.customer'))
                        ->orderable(false)
                        ->searchable(false)
                        ->alignCenter(),
                ];
            }

            return $headings;
        }, 123, 2);
    }

    public function registerTopHeaderNotification(?string $options): string
    {
        if (Auth::user()->hasPermission('booking.edit')) {
            $bookings = Booking::query()
                ->where('status', BaseStatusEnum::PENDING)
                ->select(['id', 'created_at'])
                ->with(['address'])
                ->orderByDesc('created_at')
                ->get();

            if ($bookings->isEmpty()) {
                return $options;
            }

            return $options . view('plugins/hotel::notification', compact('bookings'))->render();
        }

        return $options;
    }

    public function countPendingBookings(int|string|null $number, string $menuId): ?string
    {
        if ($menuId !== 'cms-plugins-booking') {
            return $number;
        }

        return view('core/base::partials.navbar.badge-count', ['class' => 'pending-bookings'])->render();
    }

    public function getMenuItemCount(array $data = []): array
    {
        if (Auth::user()->hasPermission('booking.index')) {
            $data[] = [
                'key'   => 'pending-bookings',
                'value' => Booking::query()
                    ->where('status', BaseStatusEnum::PENDING)
                    ->count(),
            ];
        }

        return $data;
    }
}
