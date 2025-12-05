<?php

namespace Botble\Hotel\Http\Controllers\Front;

use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Hotel\Models\CustomerCard;
use Botble\Hotel\Models\CustomerCardOrder;
use Botble\Hotel\Models\CustomerCardUsage;
use Botble\Hotel\Services\CustomerCardPurchaseService;
use Botble\Hotel\Services\CustomerCardService;
use Botble\Payment\Enums\PaymentMethodEnum;
use Botble\Payment\Services\Gateways\BankTransferPaymentService;
use Botble\Payment\Services\Gateways\CodPaymentService;
use Botble\Theme\Facades\Theme;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class CustomerDashboardController
{
    public function cards(CustomerCardService $customerCardService)
    {
        $user = auth('customer')->user();

        Theme::asset()->add('hotel-customer-card-style', 'vendor/core/plugins/hotel/css/customer-card.css', ['customer-style']);

        $cards = CustomerCard::query()
            ->where('assigned_to', $user->getKey())
            ->orderByDesc('updated_at')
            ->get();

        $cards->each(function (CustomerCard $card): void {
            if (! $card->uid && $card->assigned_to) {
                $card->uid = CustomerCard::generateUid();
                $card->save();
            }
        });

        $activeCard = $cards->first(function (CustomerCard $card) {
            return $card->is_active && $card->units_remaining > 0 && (! $card->valid_until || ! $card->valid_until->isPast());
        });

        $displayCard = $activeCard ?? $cards->first();

        $hasActiveCard = $customerCardService->customerHasActiveCard($user->getKey());

        $availableCards = CustomerCard::query()
            ->whereNull('assigned_to')
            ->active()
            ->where(function ($query) use ($user) {
                $query->where('is_single_purchase', false);

                if ($user) {
                    $query->orWhere(function ($inner) use ($user) {
                        $inner->where('is_single_purchase', true)
                            ->whereDoesntHave('orders', function ($orderQuery) use ($user) {
                                $orderQuery
                                    ->where('customer_id', $user->getKey())
                                    ->where('status', 'completed');
                            });
                    });
                }
            })
            ->orderBy('name')
            ->get()
            ->map(function (CustomerCard $card) use ($customerCardService) {
                $card->purchase_price = $customerCardService->calculatePurchasePrice($card);

                return $card;
            });

        $usages = CustomerCardUsage::query()
            ->withoutGlobalScopes()
            ->whereHas('card', function ($query) use ($user) {
                $query->where('assigned_to', $user->getKey());
            })
            ->with(['card', 'courseBooking.course'])
            ->orderByDesc('consumed_at')
            ->orderByDesc('created_at')
            ->get();

        $usages->each(function (CustomerCardUsage $usage): void {
            $date = $usage->consumed_at ?? $usage->created_at;

            $usage->display_date = $date
                ? $date->timezone(config('app.timezone'))->format('d.m.Y H:i')
                : null;
        });

        return Theme::scope(
            'hotel.customers.cards',
            compact('cards', 'availableCards', 'usages', 'activeCard', 'displayCard', 'hasActiveCard'),
            'plugins/hotel::themes.customers.cards'
        )->render();
    }

    public function checkoutCard(
        CustomerCard $customerCard,
        CustomerCardService $customerCardService,
        CustomerCardPurchaseService $purchaseService,
        BaseHttpResponse $response
    )
    {
        abort_if($customerCard->assigned_to !== null, 404);
        abort_if(! $customerCard->is_active, 404);
        abort_if($customerCard->valid_until && $customerCard->valid_until->isPast(), 404);

        $user = auth('customer')->user();

        if ($customerCardService->customerHasActiveCard($user->getKey())) {
            return $response
                ->setError()
                ->setMessage(trans('plugins/hotel::customer-card.purchase.already_active'))
                ->setNextUrl(route('customer.cards'));
        }

        if (! $customerCardService->customerCanPurchaseTemplate($customerCard, $user)) {
            return $response
                ->setError()
                ->setMessage(trans('plugins/hotel::customer-card.purchase.single_restriction'))
                ->setNextUrl(route('customer.cards'));
        }

        $purchasePrice = $customerCardService->calculatePurchasePrice($customerCard);

        $order = null;

        if (is_plugin_active('payment')) {
            $order = $purchaseService->preparePendingOrder($customerCard, $user, $purchasePrice);
        }

        $paymentMethods = [];

        if (is_plugin_active('payment') && app()->bound('payment.methods')) {
            $paymentMethods = app('payment.methods')->all();
        }

        return Theme::scope(
            'hotel.customers.card-checkout',
            compact('customerCard', 'purchasePrice', 'user', 'paymentMethods', 'order'),
            'plugins/hotel::themes.customers.card-checkout'
        )->render();
    }

    public function purchaseCard(
        CustomerCard $customerCard,
        CustomerCardService $customerCardService,
        CustomerCardPurchaseService $purchaseService,
        BaseHttpResponse $response,
        Request $request
    ): BaseHttpResponse {
        abort_if($customerCard->assigned_to !== null, 404);
        abort_if(! $customerCard->is_active, 404);
        abort_if($customerCard->valid_until && $customerCard->valid_until->isPast(), 404);

        $user = auth('customer')->user();

        if ($customerCardService->customerHasActiveCard($user->getKey())) {
            return $response
                ->setError()
                ->setNextUrl(route('customer.cards'))
                ->setMessage(trans('plugins/hotel::customer-card.purchase.already_active'));
        }

        if (! $customerCardService->customerCanPurchaseTemplate($customerCard, $user)) {
            return $response
                ->setError()
                ->setNextUrl(route('customer.cards'))
                ->setMessage(trans('plugins/hotel::customer-card.purchase.single_restriction'));
        }

        $amount = $customerCardService->calculatePurchasePrice($customerCard);

        if ($amount <= 0) {
            $assignedCard = $customerCardService->assignTemplateToCustomer($customerCard, $user);
            $purchaseService->createOrder($customerCard, $user, 0)->update([
                'status' => 'completed',
                'assigned_card_id' => $assignedCard->getKey(),
                'completed_at' => now(),
            ]);

            return $response
                ->setNextUrl(route('customer.cards.success'))
                ->setMessage(trans('plugins/hotel::customer-card.purchase.success_message'));
        }

        $order = null;

        $requestOrderId = (int) $request->input('order_id');

        if ($requestOrderId) {
            $order = CustomerCardOrder::query()
                ->whereKey($requestOrderId)
                ->where('customer_id', $user->getKey())
                ->where('card_template_id', $customerCard->getKey())
                ->where('status', 'pending')
                ->first();
        }

        if ($order) {
            if ((float) $order->amount !== (float) $amount) {
                $order->update(['amount' => $amount]);
            }

            $order->refresh();
        } else {
            $order = $purchaseService->createOrder($customerCard, $user, $amount);
        }

        if (! is_plugin_active('payment')) {
            $assignedCard = $customerCardService->assignTemplateToCustomer($customerCard, $user);

            $order->update([
                'status' => 'completed',
                'assigned_card_id' => $assignedCard->getKey(),
                'completed_at' => now(),
            ]);

            return $response
                ->setNextUrl(route('customer.cards.success'))
                ->setMessage(trans('plugins/hotel::customer-card.purchase.success_message'));
        }

        $paymentMethod = $request->input('payment_method');

        if (! $paymentMethod) {
            return $response
                ->setError()
                ->setNextUrl(route('customer.cards.checkout', $customerCard))
                ->setMessage(trans('plugins/hotel::customer-card.checkout.payment_method_missing'));
        }

        $data = [
            'error' => false,
            'message' => false,
            'amount' => $order->amount,
            'currency' => strtoupper(get_application_currency()->title),
            'type' => $paymentMethod,
            'charge_id' => null,
        ];

        session()->put('selected_payment_method', $data['type']);
        session(['order_type' => CustomerCardOrder::class]);

        $paymentData = apply_filters(PAYMENT_FILTER_PAYMENT_DATA, [
            'amount' => $amount,
            'currency' => $data['currency'],
            'order_id' => [$order->getKey()],
            'description' => trans('plugins/payment::payment.payment_description', [
                'order_id' => $order->getKey(),
                'site_url' => request()->getHost(),
            ]),
            'customer_id' => $user->getKey(),
            'customer_type' => get_class($user),
            'return_url' => $request->input('return_url', route('customer.cards.success')),
            'callback_url' => $request->input('callback_url', route('customer.cards.success')),
            'order_type' => CustomerCardOrder::class,
        ], $request);

        switch ($paymentMethod) {
            case PaymentMethodEnum::COD:
                $codPaymentService = app(CodPaymentService::class);
                $data['charge_id'] = $codPaymentService->execute($paymentData);
                $data['message'] = trans('plugins/payment::payment.payment_pending');

                break;
            case PaymentMethodEnum::BANK_TRANSFER:
                $bankTransferPaymentService = app(BankTransferPaymentService::class);
                $data['charge_id'] = $bankTransferPaymentService->execute($paymentData);
                $data['message'] = trans('plugins/payment::payment.payment_pending');

                break;
            default:
                $data = apply_filters(PAYMENT_FILTER_AFTER_POST_CHECKOUT, $data, $request);

                break;
        }

        if ($checkoutUrl = Arr::get($data, 'checkoutUrl')) {
            return $response
                ->setError($data['error'])
                ->setNextUrl($checkoutUrl)
                ->setData(['checkoutUrl' => $checkoutUrl])
                ->setMessage($data['message']);
        }

        if ($data['error'] || ! $data['charge_id']) {
            $order->update(['status' => 'failed']);

            session()->forget('selected_payment_method');
            session()->forget('order_type');

            return $response
                ->setError()
                ->setNextUrl(route('customer.cards.checkout', $customerCard))
                ->setMessage($data['message'] ?: __('Checkout error!'));
        }

        return $response
            ->setNextUrl(route('customer.cards.success'))
            ->setMessage(trans('plugins/hotel::customer-card.purchase.pending_message'));
    }

    public function successCard(CustomerCardService $customerCardService)
    {
        $customer = auth('customer')->user();

        $activeCard = CustomerCard::query()
            ->active()
            ->where('assigned_to', $customer->getKey())
            ->orderByDesc('created_at')
            ->first();

        if (! $activeCard) {
            return redirect()->route('customer.cards');
        }

        $statusTone = $activeCard->status_color ?? 'success';

        return Theme::scope(
            'hotel.customers.card-success',
            compact('activeCard', 'customer', 'statusTone'),
            'plugins/hotel::themes.customers.card-success'
        )->render();
    }
}
